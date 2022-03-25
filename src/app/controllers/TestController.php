<?php

use Phalcon\Mvc\Controller;


class TestController extends Controller
{
    public function checkconfigAction()
    {
        $config = $this->di->get("config");
        $this->view->app = $config->get("app");

        
        
        // return '<h1>Hello World!</h1>';
    }
}