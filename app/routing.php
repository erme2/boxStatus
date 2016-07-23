<?php
$app->get('/', function(Silex\Application $app) {
    return $app->abort(403, "Forbidden");
});

$app->post('/', "controller.index:indexAction");

//$app->post('/', function(Silex\Application $app) {
//    return $app->json([
//        'request' =>[
//            'endpoint'  => '/',
//            'time'      => time(),
//        ]
//    ]);
//});