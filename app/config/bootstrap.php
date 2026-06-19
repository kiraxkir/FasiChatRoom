<?php

spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/../';
    $prefix = 'App\\';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $relativePath = str_replace('\\', '/', $relativeClass) . '.php';
    $file = $baseDir . $relativePath;

    if (file_exists($file)) {
        require_once $file;
    }
});

require_once __DIR__ . '/database.php';
