<?php

namespace boxStatus\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use boxStatus\Services\ipService;
use boxStatus\Services\tokenService;
use boxStatus\Modules\ConsoleModule;
//use Linfo\Linfo;

require_once 'AncestorController.php';

Class IndexController extends Ancestor
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
        if (false !== array_search('token', $app['config']['access']))
        {
            $tokenService = new tokenService($app);
            if($tokenService->checkToken($request->get('token'), $request->get('time')) === false){
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

        $this->_getData();
        return $this->returnResult($this->response);
    }

    private function _getData()
    {
//        $linfo = new Linfo();
//        $parser = $linfo->getParser();
//        $system['box']      = $parser->getModel();
//        $system['os']       = $parser->getDistro();
//        $system['hostname'] = $parser->getHostname();
//        $system['network']  = $parser->getNet();
//        $system['uptime']   = $parser->getUpTime();

        $console = new ConsoleModule();
        $system['network'] = $console->getNetwork();
        $this->response['response']['system'] = $system;

    }
}