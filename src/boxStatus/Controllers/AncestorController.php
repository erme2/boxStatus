<?php
namespace boxStatus\Controllers;

use boxStatus\Services\AuthService;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

Class AncestorController
{
    protected $app;
    protected $authService;
    protected $response;

    var $errorMessages = [
        403 => "Forbidden",
        404 => "Not found",
        500 => "Internal Server Error",
    ];

    /**
     * AncestorController constructor.
     *
     * it setup:
     * - the Auth service
     * - the response object
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->authService = new AuthService($app);

        $this->response = [
            "request" => [
                "received"  => time(),
                "time"  => microtime(),
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

    /**
     * Returns a specific error with a specific massage
     *
     * @param $errorID
     */
    public function returError($errorID)
    {
        $this->app->abort($errorID, $this->errorMessages[$errorID]);
    }

    /**
     * Set endpoint and method on the response obj
     *
     * @param Request $request
     */
    public function setRequestOnResponse(Request $request)
    {
        $this->response ["request"]['endpoint'] = $request->getRequestUri();
        $this->response ["request"]['method']   = $request->getMethod();
    }

    /**
     * Return the result by format
     *
     * @param $result
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function returnResult($format = 'json')
    {
        $this->response['request']['time'] = ( microtime() - $this->response['request']['time']);


        switch ($format) {
            case 'html':
                return
                    "received: ".$this->response['request']['human']['received']."<br/>".
                    "execution time: ".$this->response['request']['time']."<br/>".
                    "-----------------<br/>".
                    "CPU %: ".$this->response['response']['cpu']['%cpu']."<br/>".
                    "Memory %: ".$this->response['response']['cpu']['%mem']."<br/>".
                    "-----------------<br/>".
                    "RAM Total: ".$this->response['response']['ram']['human']['MemTotal']."<br/>".
                    "RAM Free: ".$this->response['response']['ram']['human']['MemFree']."<br/>".
                    "RAM Available: ".$this->response['response']['ram']['human']['MemAvailable']."<br/>".
                    "-----------------<br/>".
                    "DISK Total: ".$this->response['response']['disks']['/']['human']['Total']."<br/>".
                    "DISK Used: ".$this->response['response']['disks']['/']['human']['Used']."<br/>".
                    "DISK Available: ".$this->response['response']['disks']['/']['human']['Available']."<br/>".
                    "-----------------<br/>";
                break;
            case 'json':
            default:
                return $this->app->json($this->response);
        }
    }
}