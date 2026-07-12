<?php

declare(strict_types=1);

use Symfony\Component\Finder\Finder;

/*
 * Regression tests for the translation audit. These tests scan the source
 * tree for the specific anti-patterns that were fixed as part of the audit
 * (hardcoded user-facing strings, English text passed as a translation key,
 * leftover Spanish strings, etc.). They are intentionally narrow so that
 * generic Laravel starter-kit __() calls (e.g. __('Settings')) that work
 * as a no-op fallback do not fail.
 */

function translationAuditFiles(): array
{
    $base = base_path();
    $paths = [
        $base . DIRECTORY_SEPARATOR . 'app',
        $base . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views',
    ];

    $files = [];
    foreach ($paths as $path) {
        if (! is_dir($path)) {
            continue;
        }

        foreach (Finder::create()->files()->in($path)->name(['*.php', '*.blade.php']) as $file) {
            $files[] = $file->getRealPath();
        }
    }

    return $files;
}

function findTranslationViolations(string $regex, array $files): array
{
    $violations = [];
    foreach ($files as $file) {
        $contents = (string) file_get_contents($file);
        $relative = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file);

        if (preg_match_all($regex, $contents, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $hit) {
                $offset = $hit[1];
                $line = mb_substr_count(mb_substr($contents, 0, $offset), "\n") + 1;
                $violations[] = sprintf('%s:%d  %s', $relative, $line, $hit[0]);
            }
        }
    }

    return $violations;
}

it('does not hardcode the role/permission label keys that were re-keyed', function (): void {
    // These were the specific English-as-key call sites fixed in the audit.
    $antiPatterns = [
        '__\(\s*[\'"]Role Name[\'"]',
        '__\(\s*[\'"]New Team[\'"]',
        '__\(\s*[\'"]Team Name[\'"]',
        '__\(\s*[\'"]Create User[\'"]',
        '__\(\s*[\'"]Select All[\'"]',
        '__\(\s*[\'"]Custom Permissions[\'"]',
        '__\(\s*[\'"]Avatar[\'"]',
        '__\(\s*[\'"]Username[\'"]',
        '__\(\s*[\'"]Role[\'"]\s*\)',
        '__\(\s*[\'"]Guard[\'"]',
        '__\(\s*[\'"]Permissions[\'"]',
        '__\(\s*[\'"]Updated At[\'"]',
        '__\(\s*[\'"]This username is already taken',
        '__\(\s*[\'"]This email is already registered',
        '__\(\s*[\'"]The team name must be a string',
        '__\(\s*[\'"]This team name is reserved',
        '__\(\s*[\'"]The email address must be a string',
        '__\(\s*[\'"]This user is already a member',
        '__\(\s*[\'"]An invitation has already been sent',
        '__\(\s*"You\'ve been invited to join',
        '__\(\s*[\'"]:inviterName has invited you to join',
        '__\(\s*[\'"]Log in and visit your dashboard',
        '__\(\s*[\'"]Log in[\'"]\s*\)',
        "__\\(\\s*'Este correo electr",
    ];

    $all = [];
    foreach ($antiPatterns as $regex) {
        foreach (findTranslationViolations('/' . $regex . '/', translationAuditFiles()) as $hit) {
            $all[] = $hit;
        }
    }

    expect($all)
        ->toBeArray()
        ->toBeEmpty(sprintf(
            "Found %d hardcoded translation call(s) that should use lang/en/ keys.\n\n%s",
            count($all),
            implode("\n", $all),
        ));
});

it('does not contain leftover Spanish strings in source files', function (): void {
    $markers = [
        'Aquí puedes gestionar',
        'Total de Usuarios',
        'Usuarios Activos',
        'Usuarios Eliminados',
        'Usuarios no eliminados',
        'Usuarios en papelera',
        'Todos los usuarios registrados',
        'perfiles de usuario de tu organizaci',
    ];

    $violations = [];
    foreach (translationAuditFiles() as $file) {
        $contents = (string) file_get_contents($file);
        $relative = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file);

        foreach ($markers as $marker) {
            if (str_contains($contents, $marker)) {
                $line = mb_substr_count(mb_substr($contents, 0, (int) mb_strpos($contents, $marker)), "\n") + 1;
                $violations[] = sprintf('%s:%d  contains "%s"', $relative, $line, $marker);
            }
        }
    }

    expect($violations)
        ->toBeArray()
        ->toBeEmpty(sprintf(
            "Found %d leftover Spanish string(s) in source files.\nReplace with __() and add the English source to lang/en/.\n\n%s",
            count($violations),
            implode("\n", $violations),
        ));
});

it('does not hardcode the brand name in blade components', function (): void {
    $regex = '/[\'"](Laravel Starter Kit)[\'"]/';

    $violations = findTranslationViolations($regex, translationAuditFiles());

    expect($violations)
        ->toBeArray()
        ->toBeEmpty(sprintf(
            "Found %d hardcoded occurrence(s) of 'Laravel Starter Kit'. Use __('app.app_logo_name') instead.\n\n%s",
            count($violations),
            implode("\n", $violations),
        ));
});

it('does not hardcode OTP code labels in blade templates', function (): void {
    $regex = '/label\s*=\s*[\'"]OTP Code[\'"]/';

    $violations = findTranslationViolations($regex, translationAuditFiles());

    expect($violations)
        ->toBeArray()
        ->toBeEmpty(sprintf(
            "Found %d hardcoded 'OTP Code' label(s). Use :label=\"__('app.otp_code')\" instead.\n\n%s",
            count($violations),
            implode("\n", $violations),
        ));
});

it('does not hardcode failed-to-load messages', function (): void {
    $regex = '/[\'"]Failed to (load|fetch) [^\'"]+[\'"]/';

    $violations = findTranslationViolations($regex, translationAuditFiles());

    expect($violations)
        ->toBeArray()
        ->toBeEmpty(sprintf(
            "Found %d hardcoded 'Failed to ...' message(s). Move them to lang/en/app.php.\n\n%s",
            count($violations),
            implode("\n", $violations),
        ));
});

it('does not hardcode common form labels in Filament component props', function (): void {
    $patterns = [
        '/->label\(\s*[\'"](First Name|Last Name)[\'"]\s*\)/',
        '/->label\(\s*[\'"](Id)[\'"]\s*\)/',
        '/->label\(\s*[\'"]Create User[\'"]\s*\)/',
        '/->placeholder\(\s*[\'"]e\.g\. admin[\'"]/',
    ];

    $all = [];
    foreach ($patterns as $regex) {
        foreach (findTranslationViolations($regex, translationAuditFiles()) as $hit) {
            $all[] = $hit;
        }
    }

    expect($all)
        ->toBeArray()
        ->toBeEmpty(sprintf(
            "Found %d hardcoded form label(s). Use __('fields.*') or __('app.*') keys instead.\n\n%s",
            count($all),
            implode("\n", $all),
        ));
});
