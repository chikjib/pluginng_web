<?php

// Preserve cPanel's existing handler/rewrites while forwarding bearer tokens to PHP.
$path = rtrim($argv[1] ?? dirname(__DIR__), '/').'/.htaccess';
try {
    if (!is_file($path)) {
        fwrite(STDOUT, "No root .htaccess; public/.htaccess already includes Authorization forwarding.\n");
        exit(0);
    }
    $original = file_get_contents($path);
    if ($original === false) throw new RuntimeException('Unable to read root .htaccess.');
    if (preg_match('/^\s*RewriteRule[^\r\n]*HTTP_AUTHORIZATION/mi', $original)) {
        fwrite(STDOUT, "Authorization forwarding is already configured.\n");
        exit(0);
    }
    $block = <<<'APACHE'
# Pluginng: forward API bearer tokens when the application root serves server.php.
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
</IfModule>

APACHE;
    $temporary = tempnam(dirname($path), '.htaccess-auth-');
    if ($temporary === false) throw new RuntimeException('Unable to prepare Authorization forwarding.');
    try {
        if (file_put_contents($temporary, $block."\n".$original) === false
            || !chmod($temporary, fileperms($path) & 0777)
            || !rename($temporary, $path)) {
            throw new RuntimeException('Unable to update Authorization forwarding. Check file ownership.');
        }
    } finally {
        if (is_file($temporary)) unlink($temporary);
    }
    fwrite(STDOUT, "Authorization forwarding added; existing cPanel settings preserved.\n");
} catch (Throwable $exception) {
    fwrite(STDERR, $exception->getMessage().PHP_EOL);
    exit(1);
}
