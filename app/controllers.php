<?php
$app->register(new Silex\Provider\RoutingServiceProvider);
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());

// linking controllers
$app['controller.index'] = function($app) {
    return new boxStatus\Controllers\indexController();
};
