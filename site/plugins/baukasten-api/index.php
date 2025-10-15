<?php

/**
 * Baukasten API Plugin
 *
 * This plugin extracts helper functions, API endpoints, and routing logic
 * from the main config.php file to create a more maintainable structure.
 */

use Kirby\Cms\App as Kirby;

// Autoload plugin classes
spl_autoload_register(function ($class) {
    // Only handle classes in our namespace
    if (strpos($class, 'BaukastenApi\\') !== 0) {
        return;
    }

    // Convert namespace to file path
    $classPath = str_replace('BaukastenApi\\', '', $class);
    $classPath = str_replace('\\', '/', $classPath);
    $file = __DIR__ . '/src/' . $classPath . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Load helper functions into global namespace
require_once __DIR__ . '/src/helpers.php';

// Plugin registration with routes
Kirby::plugin('baukasten/api', [
    'options' => [
        'cache' => true,
    ],
    'routes' => \BaukastenApi\Api\Routes::getRoutes()
]);
