<?php
$app->get('/', function(Silex\Application $app) {
    return $app->json([
        'request' =>[
            'endpoint'  => '/',
            'time'      => time(),
        ]
    ]);
});