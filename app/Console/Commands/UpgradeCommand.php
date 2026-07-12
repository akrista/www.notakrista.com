<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Upgrade\FileClassifier;
use App\Console\Commands\Upgrade\FileStatus;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\spin;

use Symfony\Component\Console\Attribute\AsCommand;

use function Termwind\render;

use ZipArchive;

#[AsCommand(name: 'bizkit:upgrade')]
#[Description('Upgrade your project by pulling in changes from the upstream bizkit skeleton.')]
#[Signature('bizkit:upgrade
                            {--dev : Compare against HEAD instead of a stable tag}
                            {--dry-run : Show what would change without applying anything}
                            {--take-all : Automatically overwrite all differing files and apply all deletions}
                            {--skip-all : Keep all local versions of differing files and skip deletions}')]
final class UpgradeCommand extends Command
{
    /**
     * The upstream GitHub repository slug.
     */
    private const string UPSTREAM_REPO = 'akrista/bizkit';

    /**
     * The local version tracking file.
     */
    private const string VERSION_FILE = 'bizkit.json';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Http::allowStrayRequests();

        render(<<<'HTML'
            <div class="mx-2 my-1">
                <div class="px-2 py-0.5 bg-blue-600 text-white font-bold uppercase">
                    🚀 Bizkit Upgrader
                </div>
                <div class="mt-1 text-gray-500">
                    Comparing your project against <span class="text-cyan-500 font-bold">akrista/bizkit</span>
                </div>
            </div>
        HTML);

        if ($this->option('take-all') && $this->option('skip-all')) {
            $this->error('Error: You cannot use both --take-all and --skip-all.');

            return self::FAILURE;
        }

        if (! $this->passesPreflight()) {
            return self::FAILURE;
        }

        $currentVersion = $this->readCurrentVersion();
        $targetRef = $this->resolveTargetRef($currentVersion);

        if ($targetRef === null) {
            return self::FAILURE;
        }

        $currentSkeletonFiles = [];
        if ($currentVersion !== null) {
            $currentSkeletonFiles = spin(
                fn (): array => array_keys($this->fetchUpstreamFiles($currentVersion)),
                'Fetching current skeleton files for version ' . $currentVersion . '…',
            );
        }

        $upstreamFiles = spin(
            fn (): array => $this->fetchUpstreamFiles($targetRef),
            'Fetching upstream files from ' . self::UPSTREAM_REPO . '@' . $targetRef . '…',
        );

        if ($upstreamFiles === []) {
            render(<<<'HTML'
                <div class="mx-2 my-1 px-2 py-1 bg-red-600 text-white font-bold rounded">
                    Could not fetch upstream files. Check your internet connection and that `gh` is authenticated.
                </div>
            HTML);

            return self::FAILURE;
        }

        $classifier = new FileClassifier($upstreamFiles, base_path(), $currentSkeletonFiles);
        $classified = $classifier->classify();

        $this->printSummary($classified);

        if ($this->option('dry-run')) {
            render(<<<'HTML'
                <div class="mx-2 my-1 text-yellow-500 font-bold">
                    Dry run complete. No changes were made.
                </div>
            HTML);

            return self::SUCCESS;
        }

        if (! confirm('Proceed with applying changes?', default: true)) {
            render(<<<'HTML'
                <div class="mx-2 my-1 text-red-500 font-bold">
                    Aborted. No changes were made.
                </div>
            HTML);

            return self::SUCCESS;
        }

        $this->applyChanges($classified, $upstreamFiles);
        $this->updateVersionFile($targetRef);

        render(<<<'HTML'
            <div class="mx-2 my-2 px-2 py-1 bg-green-600 text-white font-bold">
                🎉 Upgrade complete!
            </div>
            <div class="mx-2 text-gray-400">
                Please review applied changes with <span class="text-cyan-400 font-bold">git diff HEAD</span>
            </div>
        HTML);

        return self::SUCCESS;
    }

    /**
     * Run preflight checks: git available, clean working tree.
     */
    private function passesPreflight(): bool
    {
        // Check git is available
        if (! $this->commandExists('git')) {
            render(<<<'HTML'
                <div class="mx-2 my-1 px-2 py-1 bg-red-600 text-white font-bold">
                    Error: git is not installed or available in the system PATH.
                </div>
            HTML);

            return false;
        }

        // Check for clean working tree
        $statusCheck = Process::run(['git', 'status', '--porcelain']);
        if (! $statusCheck->successful()) {
            render(<<<'HTML'
                <div class="mx-2 my-1 px-2 py-1 bg-red-600 text-white font-bold">
                    Error: Could not determine git status. Are you inside a git repository?
                </div>
            HTML);

            return false;
        }

        if (mb_trim($statusCheck->output()) !== '') {
            render(<<<'HTML'
                <div class="mx-2 my-1 px-2 py-1 bg-yellow-600 text-black font-bold">
                    Uncommitted Changes Detected
                </div>
                <div class="mx-2 text-gray-400">
                    Please commit or stash your changes before upgrading.
                </div>
            HTML);
            $this->line($statusCheck->output());

            return false;
        }

        return true;
    }

    /**
     * Read the currently pinned version from bizkit.json.
     */
    private function readCurrentVersion(): ?string
    {
        $versionFile = base_path(self::VERSION_FILE);

        if (! file_exists($versionFile)) {
            render(sprintf(<<<'HTML'
                <div class="mx-2 my-1 text-yellow-500 font-bold">
                    [Warning] %s not found. Assuming fresh project.
                </div>
            HTML, self::VERSION_FILE));

            return null;
        }

        /** @var array{version?: string, repository?: string} $data */
        $data = json_decode((string) file_get_contents($versionFile), associative: true) ?? [];

        return $data['version'] ?? null;
    }

    /**
     * Resolve the upstream git ref to compare against.
     * Uses the latest tag by default, or HEAD when --dev is passed.
     */
    private function resolveTargetRef(?string $currentVersion): ?string
    {
        if ($this->option('dev')) {
            render(<<<'HTML'
                <div class="mx-2 text-yellow-500 font-bold">
                    [Option] Using HEAD (--dev flag set).
                </div>
            HTML);

            return 'HEAD';
        }

        $tags = spin(
            function (): array {
                $response = Http::withHeaders(array_merge([
                    'User-Agent' => 'bizkit-upgrader/1.0',
                    'Accept' => 'application/vnd.github+json',
                ], $this->getAuthHeader()))->get('https://api.github.com/repos/' . self::UPSTREAM_REPO . '/tags');

                if (! $response->successful()) {
                    return [];
                }

                /** @var array<array{name: string}> $data */
                $data = $response->json() ?? [];

                return array_column($data, 'name');
            },
            'Fetching available upstream tags…',
        );

        if ($tags === []) {
            render(<<<'HTML'
                <div class="mx-2 my-1 px-2 py-1 bg-red-600 text-white font-bold">
                    No tags found on akrista/bizkit. Use --dev to compare against HEAD.
                </div>
            HTML);

            return null;
        }

        // Latest tag is first in the GitHub response
        $latestTag = (string) reset($tags);

        if ($currentVersion !== null && $currentVersion === $latestTag) {
            render(sprintf(<<<'HTML'
                <div class="mx-2 my-1 px-2 py-1 bg-green-600 text-white font-bold">
                    You're already on the latest version (%s).
                </div>
                <div class="mx-2 mt-1 text-gray-500">
                    Nothing to upgrade.
                </div>
            HTML, $latestTag));

            return null;
        }

        if ($currentVersion !== null) {
            render(sprintf(<<<'HTML'
                <div class="mx-2 my-1">
                    <span class="text-gray-500">Upgrade path:</span>
                    <span class="text-red-500 font-bold">%s</span>
                    <span class="text-gray-500">→</span>
                    <span class="text-green-500 font-bold">%s</span>
                </div>
            HTML, $currentVersion, $latestTag));
        } else {
            render(sprintf(<<<'HTML'
                <div class="mx-2 my-1">
                    <span class="text-gray-500">Latest upstream tag:</span>
                    <span class="text-green-500 font-bold">%s</span>
                </div>
            HTML, $latestTag));
        }

        return $latestTag;
    }

    /**
     * Fetch all files from the upstream repo at the given ref via the GitHub API.
     *
     * @return array<string, string> Map of relative path => raw content.
     */
    private function fetchUpstreamFiles(string $ref): array
    {
        $url = 'https://api.github.com/repos/' . self::UPSTREAM_REPO . '/zipball/' . $ref;

        $response = Http::withHeaders(array_merge([
            'User-Agent' => 'bizkit-upgrader/1.0',
        ], $this->getAuthHeader()))->get($url);

        if (! $response->successful()) {
            return [];
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'bizkit_zip_');
        file_put_contents($tempFile, $response->body());

        $zip = new ZipArchive;
        $files = [];

        if ($zip->open($tempFile) === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                if ($stat === false) {
                    continue;
                }

                $zipPath = $stat['name'];

                $parts = explode('/', str_replace('\\', '/', $zipPath));
                if (count($parts) <= 1) {
                    continue;
                }

                array_shift($parts);
                $relativePath = implode('/', $parts);

                if (str_ends_with($zipPath, '/')) {
                    continue;
                }

                if ($this->shouldSkipPath($relativePath)) {
                    continue;
                }

                $content = $zip->getFromIndex($i);
                if ($content !== false) {
                    $files[$relativePath] = $content;
                }
            }

            $zip->close();
        }

        @unlink($tempFile);

        return $files;
    }

    /**
     * Whether a path should be excluded from classification entirely.
     */
    private function shouldSkipPath(string $path): bool
    {
        $skippedPrefixes = [
            'vendor/',
            'node_modules/',
            '.git/',
        ];

        $skippedFiles = [
            'composer.lock',
            'bun.lock',
            'package-lock.json',
            'yarn.lock',
            'pnpm-lock.yaml',
        ];

        foreach ($skippedPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return in_array($path, $skippedFiles, strict: true);
    }

    /**
     * Print a summary table of classified files.
     *
     * @param  array<string, FileStatus>  $classified
     */
    private function printSummary(array $classified): void
    {
        $grouped = [];
        foreach ($classified as $path => $status) {
            $grouped[$status->value][] = $path;
        }

        $newCount = count($grouped[FileStatus::New->value] ?? []);
        $differsCount = count($grouped[FileStatus::Differs->value] ?? []);
        $alreadyCount = count($grouped[FileStatus::AlreadyPresent->value] ?? []);
        $deletedCount = count($grouped[FileStatus::DeletedUpstream->value] ?? []);

        render(sprintf(<<<'HTML'
            <div class="mx-2 my-2">
                <div class="font-bold text-gray-400 mb-1">Upgrade Summary:</div>
                <table class="w-full">
                    <tr>
                        <td class="text-green-500 font-bold w-1/3">New Files</td>
                        <td class="text-right text-green-500 font-bold">%d</td>
                    </tr>
                    <tr>
                        <td class="text-yellow-500 font-bold">Differing Files</td>
                        <td class="text-right text-yellow-500 font-bold">%d</td>
                    </tr>
                    <tr>
                        <td class="text-red-500 font-bold">Deleted Upstream</td>
                        <td class="text-right text-red-500 font-bold">%d</td>
                    </tr>
                    <tr>
                        <td class="text-gray-500">Already Up-to-Date</td>
                        <td class="text-right text-gray-500">%d</td>
                    </tr>
                </table>
            </div>
        HTML, $newCount, $differsCount, $deletedCount, $alreadyCount));

        if ($newCount > 0) {
            render(<<<'HTML'
                <div class="mx-2 mt-2">
                    <div class="text-green-500 font-bold">New Files (will be applied automatically):</div>
                </div>
            HTML);
            foreach ($grouped[FileStatus::New->value] as $path) {
                render(sprintf(<<<'HTML'
                    <div class="mx-4 text-green-400">
                        <span>+</span> <span class="ml-1">%s</span>
                    </div>
                HTML, $path));
            }
        }

        if ($differsCount > 0) {
            render(<<<'HTML'
                <div class="mx-2 mt-2">
                    <div class="text-yellow-500 font-bold">Files that differ (you will decide for each):</div>
                </div>
            HTML);
            foreach ($grouped[FileStatus::Differs->value] as $path) {
                render(sprintf(<<<'HTML'
                    <div class="mx-4 text-yellow-400">
                        <span>~</span> <span class="ml-1">%s</span>
                    </div>
                HTML, $path));
            }
        }

        if ($deletedCount > 0) {
            render(<<<'HTML'
                <div class="mx-2 mt-2">
                    <div class="text-red-500 font-bold">Deleted upstream (you will decide for each):</div>
                </div>
            HTML);
            foreach ($grouped[FileStatus::DeletedUpstream->value] as $path) {
                render(sprintf(<<<'HTML'
                    <div class="mx-4 text-red-400">
                        <span>-</span> <span class="ml-1">%s</span>
                    </div>
                HTML, $path));
            }
        }
    }

    /**
     * Apply new files automatically and walk user through differing files.
     *
     * @param  array<string, FileStatus>  $classified
     * @param  array<string, string>  $upstreamFiles
     */
    private function applyChanges(array $classified, array $upstreamFiles): void
    {
        foreach ($classified as $path => $status) {
            match ($status) {
                FileStatus::New => $this->applyNewFile($path, $upstreamFiles[$path]),
                FileStatus::Differs => $this->walkUserThroughDiff($path, $upstreamFiles[$path]),
                FileStatus::DeletedUpstream => $this->walkUserThroughDeletion($path),
                FileStatus::AlreadyPresent => null, // nothing to do
            };
        }
    }

    /**
     * Write a new file from upstream into the local project.
     */
    private function applyNewFile(string $relativePath, string $content): void
    {
        $localPath = base_path($relativePath);
        $directory = dirname($localPath);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, recursive: true);
        }

        file_put_contents($localPath, $content);
        render(sprintf(<<<'HTML'
            <div class="mx-2 text-green-500 font-bold">
                ✓ Applied: <span class="text-white">%s</span>
            </div>
        HTML, $relativePath));
    }

    /**
     * Show the diff for a changed file and let the user decide.
     */
    private function walkUserThroughDiff(string $relativePath, string $upstreamContent): void
    {
        if ($this->option('take-all')) {
            file_put_contents(base_path($relativePath), $upstreamContent);
            render(sprintf(<<<'HTML'
                <div class="mx-2 text-green-500 font-bold">
                    ✓ Overwritten with upstream version (automatic): <span class="text-white">%s</span>
                </div>
            HTML, $relativePath));

            return;
        }

        if ($this->option('skip-all')) {
            render(sprintf(<<<'HTML'
                <div class="mx-2 text-gray-400">
                    – Kept local version (automatic): <span class="text-white">%s</span>
                </div>
            HTML, $relativePath));

            return;
        }

        render(sprintf(<<<'HTML'
            <div class="mx-2 mt-2 px-1 bg-yellow-600 text-black font-bold">
                ~ Differs: %s
            </div>
        HTML, $relativePath));

        // Write upstream content to a temp file for diffing
        $tempFile = tempnam(sys_get_temp_dir(), 'bizkit_upstream_');
        file_put_contents($tempFile, $upstreamContent);

        $diff = Process::run([
            'git',
            'diff',
            '--no-index',
            '--',
            base_path($relativePath),
            $tempFile,
        ]);
        $this->line($diff->output());

        @unlink($tempFile);

        $choice = select(
            label: sprintf('What would you like to do with %s?', $relativePath),
            options: [
                'keep' => 'Keep my version (skip)',
                'take' => 'Take upstream version (overwrites your changes)',
            ],
            default: 'keep',
        );

        if ($choice === 'take') {
            file_put_contents(base_path($relativePath), $upstreamContent);
            render(sprintf(<<<'HTML'
                <div class="mx-2 text-green-500 font-bold">
                    ✓ Overwritten with upstream version: <span class="text-white">%s</span>
                </div>
            HTML, $relativePath));
        } else {
            render(sprintf(<<<'HTML'
                <div class="mx-2 text-gray-400">
                    – Kept local version: <span class="text-white">%s</span>
                </div>
            HTML, $relativePath));
        }
    }

    /**
     * Surface a file that was deleted upstream and let the user decide.
     */
    private function walkUserThroughDeletion(string $relativePath): void
    {
        if ($this->option('take-all')) {
            @unlink(base_path($relativePath));
            render(sprintf(<<<'HTML'
                <div class="mx-2 text-red-500 font-bold">
                    ✓ Deleted (automatic): <span class="text-white">%s</span>
                </div>
            HTML, $relativePath));

            return;
        }

        if ($this->option('skip-all')) {
            render(sprintf(<<<'HTML'
                <div class="mx-2 text-gray-400">
                    – Kept local file (automatic): <span class="text-white">%s</span>
                </div>
            HTML, $relativePath));

            return;
        }

        render(sprintf(<<<'HTML'
            <div class="mx-2 mt-2 px-1 bg-red-600 text-white font-bold">
                - Deleted Upstream: %s
            </div>
        HTML, $relativePath));

        $shouldDelete = confirm(
            label: sprintf('Delete %s from your project?', $relativePath),
            default: false,
        );

        if ($shouldDelete) {
            @unlink(base_path($relativePath));
            render(sprintf(<<<'HTML'
                <div class="mx-2 text-red-500 font-bold">
                    ✓ Deleted: <span class="text-white">%s</span>
                </div>
            HTML, $relativePath));
        } else {
            render(sprintf(<<<'HTML'
                <div class="mx-2 text-gray-400">
                    – Kept local file: <span class="text-white">%s</span>
                </div>
            HTML, $relativePath));
        }
    }

    /**
     * Pin the newly applied version into bizkit.json.
     */
    private function updateVersionFile(string $newVersion): void
    {
        $versionFile = base_path(self::VERSION_FILE);

        $data = [
            'version' => $newVersion,
            'repository' => self::UPSTREAM_REPO,
        ];

        file_put_contents($versionFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
        render(sprintf(<<<'HTML'
            <div class="mx-2 mt-1 text-green-500 font-bold">
                ✓ Updated <span class="text-white">%s</span> to <span class="text-white">%s</span>
            </div>
        HTML, self::VERSION_FILE, $newVersion));
    }

    /**
     * Get the Authorization header for GitHub API requests if available.
     *
     * @return array<string, string>
     */
    private function getAuthHeader(): array
    {
        $token = config('bizkit.github_token');

        if (! $token && $this->commandExists('gh')) {
            $tokenResult = Process::run(['gh', 'auth', 'token']);
            if ($tokenResult->successful()) {
                $token = mb_trim($tokenResult->output());
            }
        }

        if (is_string($token) && $token !== '') {
            return ['Authorization' => 'Bearer ' . $token];
        }

        return [];
    }

    /**
     * Check whether a shell command is available on the system.
     */
    private function commandExists(string $command): bool
    {
        $check = Process::run(PHP_OS_FAMILY === 'Windows' ? ['where', $command] : ['which', $command]);

        return $check->successful();
    }
}
