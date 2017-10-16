<?php

$app->get ('/',     "controller.index:awsAction");
$app->get ('/live', "controller.index:indexAction");
$app->post('/',     "controller.index:boxMasterAction");
