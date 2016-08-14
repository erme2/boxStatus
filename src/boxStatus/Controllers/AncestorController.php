<?php
namespace boxStatus\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

Class Ancestor
{
    var $errorMessages = [
        403 => "Forbidden",
        500 => "Internal Server Error",
    ];

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->response = [
            "request" => [
                "received"  => time(),
                "time"  => microtime(),
            ]
        ];
    }

    public function returError(Application $app, $errorID)
    {
        $app->abort($errorID, $this->errorMessages[$errorID]);
    }

    public function setRequestOnResponse(Request $request)
    {
        $this->response ["request"]['endpoint'] = $request->getRequestUri();
        $this->response ["request"]['method'] = $request->getMethod();
    }

    public function returnResult($result)
    {
        $result['request']['time'] = ( microtime() - $this->response['request']['time']);
        return $this->app->json($result);
    }
}