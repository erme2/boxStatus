<?php
namespace boxStatus\Services;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class AuthService extends AncestorService
{

    protected $ipService;
    protected $tokenService;

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->ipService    = new IpService($app);
        $this->tokenService = new TokenService($app);
    }

    /**
     * Checks if you are allowed to access the data
     *
     * @return boolean
     */
    public function check(Request $request)
    {
        // do we need to check the ip?
        if (false === array_search('ip', $this->app['config']['access'])) {
            // we don't
        } else {
            // we do
            return $this->ipService->checkIpList($this->app['config']['ip_list']);
        }

        // do we need to check the token?
        if (false === array_search('token', $this->app['config']['access'])) {
            // we don't
        } else {
            // we do
            return $this->tokenService->checkToken(
                $request->get('token'),
                $request->get('time')
            );
        }

        # no access granted
        return false;
    }

}