<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\GitHubRepo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Override;

final class SyncGitHubRepos extends Command
{
    #[Override]
    protected $signature = 'github:sync-repos {--user=akrista : GitHub username}';

    #[Override]
    protected $description = 'Sync all public repositories from GitHub API';

    public function handle(): int
    {
        $username = $this->option('user');
        $token = config('services.github.token');

        if (! $token) {
            $this->error('GitHub token not configured. Set GITHUB_TOKEN in your .env file.');

            return self::FAILURE;
        }

        $this->info('Syncing repositories for user: ' . $username);

        $page = 1;
        $perPage = 100;
        $totalSynced = 0;

        do {
            $response = Http::withToken($token)
                ->timeout(30)
                ->get(sprintf('https://api.github.com/users/%s/repos', $username), [
                    'type' => 'all',
                    'sort' => 'updated',
                    'direction' => 'desc',
                    'per_page' => $perPage,
                    'page' => $page,
                ]);

            if (! $response->successful()) {
                $this->error(sprintf('GitHub API error: %d - %s', $response->status(), $response->body()));

                return self::FAILURE;
            }

            $repos = $response->json();

            if (empty($repos)) {
                break;
            }

            foreach ($repos as $repo) {
                GitHubRepo::query()->updateOrCreate(['full_name' => $repo['full_name']], [
                    'name' => $repo['name'],
                    'description' => $repo['description'] ?? null,
                    'html_url' => $repo['html_url'],
                    'language' => $repo['language'] ?? null,
                    'stargazers_count' => $repo['stargazers_count'] ?? 0,
                    'forks_count' => $repo['forks_count'] ?? 0,
                    'open_issues_count' => $repo['open_issues_count'] ?? 0,
                    'visibility' => $repo['visibility'] ?? 'public',
                    'last_push_at' => $repo['pushed_at'] ?? null,
                    'synced_at' => now(),
                ]);

                $totalSynced++;
            }

            $this->line(sprintf('  Page %d: synced ', $page) . count($repos) . ' repositories');

            $page++;
        } while (count($repos) === $perPage);

        $this->info('Sync complete. Total repositories synced: ' . $totalSynced);

        return self::SUCCESS;
    }
}
