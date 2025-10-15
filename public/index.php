<?php

use Kirby\Cms\App as Kirby;

define('KIRBY_HELPER_DUMP', false);

// Check if the autoload.php file exists
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
  die('The autoload.php file does not exist. Please run `composer install` in the root directory of your project.');
}

include $autoloadPath;

$kirby = new Kirby([
  'roots' => [
    'index'    => __DIR__,
    'base'     => $base    = dirname(__DIR__),
    'content'  => $base . '/content',
    'site'     => $base . '/site',
    'storage'  => $storage = $base . '/storage',
    'accounts' => $storage . '/accounts',
    'cache'    => $storage . '/cache',
    'sessions' => $storage . '/sessions',
  ]
]);

echo $kirby->render();
