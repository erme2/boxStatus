<?php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = APP_ENV;

// TODO: put settings here

include 'routing.php';


$app->run();