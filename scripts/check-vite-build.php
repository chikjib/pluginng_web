<?php

// Read-only release gate: refuse to reopen production with missing Vite assets.
$root = $argv[1] ?? dirname(__DIR__);
$directory = rtrim($root, '/').'/public/build/';
try {
    if (!is_file($directory.'manifest.json')) {
        throw new RuntimeException('Vite manifest is missing. Run npm run build before deployment.');
    }
    $manifest = json_decode(file_get_contents($directory.'manifest.json'), true, flags: JSON_THROW_ON_ERROR);
    foreach (['resources/js/app.js', 'resources/sass/app.scss'] as $entry) {
        if (empty($manifest[$entry]['file'])) {
            throw new RuntimeException('A required application entry is missing from the Vite manifest.');
        }
    }
    foreach ($manifest as $entry) {
        foreach (array_merge([$entry['file'] ?? ''], $entry['css'] ?? [], $entry['assets'] ?? []) as $asset) {
            if ($asset === '' || !is_file($directory.$asset)) {
                throw new RuntimeException('A generated Vite asset is missing from this release.');
            }
        }
    }
    fwrite(STDOUT, 'Vite manifest and generated assets verified.'.PHP_EOL);
} catch (Throwable $exception) {
    fwrite(STDERR, $exception->getMessage().PHP_EOL);
    exit(1);
}
