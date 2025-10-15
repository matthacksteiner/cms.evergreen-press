<?php

// Test bootstrap for Baukasten API Plugin tests

// Include Composer autoloader
require_once __DIR__ . '/../../../../vendor/autoload.php';

// Manually load plugin classes
spl_autoload_register(function ($class) {
    // Only handle classes in our namespace
    if (strpos($class, 'BaukastenApi\\') !== 0) {
        return;
    }

    // Convert namespace to file path
    $classPath = str_replace('BaukastenApi\\', '', $class);
    $classPath = str_replace('\\', '/', $classPath);
    $file = __DIR__ . '/../src/' . $classPath . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Mock global functions that might be needed for testing
if (!function_exists('site')) {
    function site()
    {
        return $GLOBALS['mockSite'] ?? null;
    }
}

if (!function_exists('option')) {
    function option($key, $default = null)
    {
        $options = [
            'prefixDefaultLocale' => false,
        ];
        return $options[$key] ?? $default;
    }
}

// Note: getLinkArray function is provided by baukasten-field-methods plugin