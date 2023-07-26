<?php
/**
 * @author DiZed Team
 * @copyright Copyright (c) DiZed Team (https://github.com/di-zed/)
 */
spl_autoload_register(function ($class)
{
    $prefix = 'DiZed\\RepairableClient\\';
    $baseDir = __DIR__ . '/src/';

    $length = strlen($prefix);
    if (strncmp($prefix, $class, $length) !== 0) {
        return;
    }

    $relativeClass = substr($class, $length);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});
