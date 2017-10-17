<?php

$app->get ('/',         function () { return ":)"; });
$app->get ('/status',   "controller.index:indexAction");
$app->post('/',         "controller.index:boxMasterAction");
