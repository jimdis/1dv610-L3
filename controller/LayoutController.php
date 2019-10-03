<?php

namespace Controller;

class LayoutController
{

    private static $registerQuery = 'register';
    private $header = 'Assignment 3';
    private $view;
    private $loginController;


    public function __construct(\View\LayoutView $view)
    {
        $this->view = $view;
        $this->loginController = new \Controller\LoginController(new \View\LoginView());
        $this->registerController = new \Controller\RegisterController(new \View\RegisterView());
    }

    public function updateView(): void
    {
        $this->view->setHeader($this->header);

        if ($this->view->getQuery() == self::$registerQuery) {
            $this->view->setContainer($this->registerController->getView());
        } else {
            $this->view->setContainer($this->loginController->getView());
        }
    }
}
