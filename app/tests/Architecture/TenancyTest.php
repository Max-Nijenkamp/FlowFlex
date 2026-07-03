<?php

declare(strict_types=1);

test('withoutGlobalScope(CompanyScope) is forbidden outside admin + support layers', function () {
    $allowedPrefixes = [
        app_path('Filament/Admin'),
        app_path('Support'),
    ];

    $offenders = [];

    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(app_path()));
    foreach ($files as $file) {
        if ($file->isDir() || $file->getExtension() !== 'php') {
            continue;
        }

        $path = $file->getPathname();
        $source = file_get_contents($path);

        if (! str_contains($source, 'withoutGlobalScope(CompanyScope')) {
            continue;
        }

        $allowed = false;
        foreach ($allowedPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                $allowed = true;
                break;
            }
        }

        if (! $allowed) {
            $offenders[] = $path;
        }
    }

    expect($offenders)->toBeEmpty();
});
