<?php

namespace boxStatus\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

Class indexController
{
    public function indexAction (Request $request, Application $app) {
        die('ok');
    }
}