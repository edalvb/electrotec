<?php
/**
 * PSR-4 simple autoloader for the App namespace.
 * Maps App\* to this directory.
 */

// Cargar el autoloader de Composer primero (para dependencias externas)
$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/';

    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return; // not our namespace
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($file)) {
        require $file;
    }
});
