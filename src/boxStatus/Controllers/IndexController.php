<?php

namespace boxStatus\Controllers;

use boxStatus\Services\tokenService;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

use boxStatus\Services\ipService;
require_once 'AncestorController.php';

Class indexController extends Ancestor
{
    public function indexAction (Request $request, Application $app)
    {

        if(APP_ENV == 'dev')
        {
            $this->setRequestOnResponse($request);
        }

        // do we need to check the ip?
        if (false !== array_search('ip', $app['config']['access']))
        {
            // CHECK IP ADDRESS
            $ipService = new ipService($app);
            if($ipService->checkIpList($app['config']['ip_list']) === false){
                if(APP_ENV == 'dev'){
                    $this->response['response'] = [
                        'errorID'       => 403,
                        'errorMessage'  => $this->errorMessages[403]." (wrong ip)",
                    ];
                    return $this->returnResult($this->response);
                } else {
                    return $this->returError($app,403);
                }
            }
        }
        // do we need to check the token?
        if (array_search('token', $app['config']['access']))
        {
            $tokenService = new tokenService($app);
            if($tokenService->checkToken($request->get('token'), $request->get('time'))){
                // TODO get the data we want to return
                return $this->returnResult($this->response);
            } else {
                if(APP_ENV == 'dev'){
                    $this->response['response'] = [
                        'errorID'       => 403,
                        'errorMessage'  => $this->errorMessages[403]." (wrong token)",
                    ];
                    return $this->returnResult($this->response);
                } else {
                    return $this->returError($app,403);
                }
            }
        }
    }
}