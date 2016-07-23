<?php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = APP_ENV == "dev" ? true : false;

// getting the config
use Symfony\Component\Yaml;
$app['config'] = Yaml\Yaml::parse(
    file_get_contents(__DIR__.'/config/config_'.APP_ENV.'.yml')
);

include 'controllers.php';
include 'routing.php';

$app->run();