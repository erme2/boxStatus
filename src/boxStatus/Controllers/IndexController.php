<?php

namespace boxStatus\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use boxStatus\Services\ipService;
use boxStatus\Services\tokenService;
use boxStatus\Modules\ConsoleModule;


Class IndexController extends AncestorController
{
    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->response = [
            "request" => [
                "received"  => time(),
                "time"  => microtime(true),
            ]
        ];

        if(
            isset($app['config']['result']['human']) &&
            $app['config']['result']['human']
        ){
            $this->response["request"]["human"] = [
                "received"  => date("d/m/Y H:i:s"),
            ];
        }

    }

    public function indexAction (Request $request)
    {
        if($this->authService->check($request)) {
            $this->_getData();
            return self::_returnResult('html');
        } else {
            return $this->app->abort(403, "Forbidden");
        }
    }


    /**
     * TO BE REWRITE
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|void
     */
    public function boxMasterAction (Request $request)
    {

        if(APP_ENV == 'dev')
        {
            $this->setRequestOnResponse($request);
        }

        // do we need to check the ip?
        if (false !== array_search('ip', $this->app['config']['access']))
        {
            // CHECK IP ADDRESS
            $ipService = new ipService($this->app);
            if($ipService->checkIpList($this->app['config']['ip_list']) === false){
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
        if (false !== array_search('token', $this->app['config']['access']))
        {
            $tokenService = new tokenService($this->app);
            if($tokenService->checkToken($request->get('token'), $request->get('time')) === false){
                if(APP_ENV == 'dev'){
                    $this->response['response'] = [
                        'errorID'       => 403,
                        'errorMessage'  => $this->errorMessages[403]." (wrong token)",
                    ];
                    return $this->returnResult($this->response);
                } else {
                    return $this->returError($this->app,403);
                }
            }
        }

        $this->_getData();

        return $this->returnResult($this->response);
    }

    private function _getData($human = false)
    {
        $console = new ConsoleModule();
        $this->response['response'] = $console->getDinamic($this->app['config']);
    }

    private function _getStaticData ()
    {
        // TODO complete this function
        // TODO add somewhere a param to fire this function
        $console = new ConsoleModule($this->app);
        $system['network'] = $console->getStatic();
        $this->response['response']['system'] = $system;

    }


    /**
     * Return the result by format
     *
     * @param string $format
     * @return string|\Symfony\Component\HttpFoundation\JsonResponse
     */
    private function _returnResult($format = 'json')
    {
        $this->response['request']['time'] = (
            microtime(true) -
            $this->response['request']['time']
        );


        switch ($format) {
            case 'html':
                return
                    "received: ".$this->response['request']['human']['received']."<br/>".
                    "execution time: git ".$this->response['request']['time']." seconds<br/>".
                    "---------------------------------------------------<br/>".
                    "CPU %: ".$this->response['response']['cpu']['%cpu']."<br/>".
                    "Memory %: ".$this->response['response']['cpu']['%mem']."<br/>".
                    "---------------------------------------------------<br/>".
                    "RAM Total: ".$this->response['response']['ram']['human']['MemTotal']."<br/>".
                    "RAM Free: ".$this->response['response']['ram']['human']['MemFree']."<br/>".
                    "RAM Available: ".$this->response['response']['ram']['human']['MemAvailable']."<br/>".
                    "---------------------------------------------------<br/>".
                    "DISK Total: ".$this->response['response']['disks']['/']['human']['Total']."<br/>".
                    "DISK Used: ".$this->response['response']['disks']['/']['human']['Used']."<br/>".
                    "DISK Available: ".$this->response['response']['disks']['/']['human']['Available']."<br/>".
                    "---------------------------------------------------<br/>";
                break;
            case 'json':
            default:
                return $this->app->json($this->response);
        }
    }

}