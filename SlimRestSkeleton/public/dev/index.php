<?php

use AWurth\Config\ConfigurationLoader;
use Slim\App;

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

session_start();

require __DIR__ . '/../../vendor/autoload.php';

$app = new App([
    'env' => 'dev',
    'root_dir' => dirname(__DIR__, 2),
    'settings' => [
        'displayErrorDetails' => true
    ]
]);
$container = $app->getContainer();

$loader = new ConfigurationLoader(__DIR__ . '/../../var/cache/dev/config.php', true);
$loader->setParameters([
    'env'      => $container['env'],
    'root_dir' => $container['root_dir']
]);

$container['config'] = $loader->load(__DIR__ . '/../../app/config/config.dev.yml');

require __DIR__ . '/../../app/dependencies.php';

require __DIR__ . '/../../app/handlers.php';

require __DIR__ . '/../../app/middleware.php';

require __DIR__ . '/../../app/controllers.php';

require __DIR__ . '/../../app/routes.php';

$app->run();
