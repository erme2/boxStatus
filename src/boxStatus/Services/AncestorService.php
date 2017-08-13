<?php
namespace boxStatus\Services;

use Silex\Application;

class AncestorService
{

    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}