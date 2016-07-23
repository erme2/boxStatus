<?php

namespace boxStatus\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

Class indexController
{
    public function indexAction (Request $request, Application $app) {

        // do we need to check the ip?
        if (array_search('ip', $app['config']['access'])){
// TODO CHECK ADDRESS
        }
        // do we need to check the token?
        if (array_search('ip', $app['config']['access'])){
// TODO CHECK TOKEN
        }

    }
}