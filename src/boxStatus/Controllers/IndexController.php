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
            // CHECK IP ADDRESS
            $ipService = new ipService($app);
            if($ipService->checkIpList($app['config']['ip_list']) === false){
                $app->abort(403, "Forbidden");
            }
        }

        // do we need to check the token?
        if (array_search('token', $app['config']['access'])){
// TODO CHECK TOKEN
        }

        return $app->json(
            ["done"=>time()]
        );
    }
}