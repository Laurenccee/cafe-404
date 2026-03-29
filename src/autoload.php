<?php
// src/autoload.php

spl_autoload_register(function ($class) {
    // 1. We are looking for classes starting with "App\"
    $prefix = 'App\\';

    // 2. Map "App\" to your "src/" directory
    $base_dir = __DIR__ . '/';

    // 3. Check if the class uses our prefix
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // 4. Transform namespace to file path (App\Shared\Config\Database -> Shared/Config/Database.php)
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // 5. Load it
    if (file_exists($file)) {
        require_once $file;
    }
});