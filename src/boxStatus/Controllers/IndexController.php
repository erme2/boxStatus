<?php

namespace boxStatus\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

use boxStatus\Services\ipService;

Class indexController
{
    public function indexAction (Request $request, Application $app) {

        // do we need to check the ip?
        if (false !== array_search('ip', $app['config']['access'])){
// TODO CHECK ADDRESS
            $ipService = new ipService();
            $ipService->checkIpList($app['config']['ip_list']);
        }

        // do we need to check the token?
        if (array_search('token', $app['config']['access'])){
// TODO CHECK TOKEN
        }

    }
}