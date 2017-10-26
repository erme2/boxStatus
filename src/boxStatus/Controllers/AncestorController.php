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

}