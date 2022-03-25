<?php

use Phalcon\Mvc\Controller;


class IndexController extends Controller
{
    public function indexAction()
    {
        
        
        // return '<h1>Hello World!</h1>';
    }

    public function adduserAction()
    {
         $user = new Users();

        //assign value from the form to $user
        $user->assign(
            $this->request->getPost(),
            [
                'name',
                'email'
            ]
        );

        // Store and check for errors
        $success = $user->save();

        $this->response->redirect('index');


    }
}