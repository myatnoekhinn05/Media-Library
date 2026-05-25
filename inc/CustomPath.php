<?php
spl_autoload_register(function ($class) {

    $baseDir = dirname(__DIR__) . '/';

    $paths = [
        'Controller/',
        'Model/Service/',
        'Model/Repository/',
        'view/',           // ItemView lives here
        'inc/',             // Database, helpers if class-based
    ];

    foreach ($paths as $path) {
        $file = $baseDir . $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
