<?php

require_once 'Config.php';
require_once 'view/View.php';
require_once 'view/LoginView.php';
require_once 'view/RegisterView.php';
require_once 'view/DateTimeView.php';
require_once 'view/LayoutView.php';
require_once 'view/HTMLPageView.php';
require_once 'controller/Controller.php';
require_once 'controller/LayoutController.php';
require_once 'controller/LoginController.php';
require_once 'controller/RegisterController.php';
require_once 'model/Cookie.php';
require_once 'model/UserStorage.php';
require_once 'model/Database.php';
require_once 'model/UserDAL.php';
require_once 'model/User.php';
require_once 'model/Messages.php';
require_once 'model/FormAction.php';


class Application
{
    private $view;
    private $controller;

    public function __construct()
    {
        $storage = new \Model\UserStorage();
        $this->view = new \View\LayoutView($storage);
        $this->controller = new \Controller\LayoutController($this->view, $storage);
    }

    public function run()
    {
        $this->changeState();
        $this->generateOutput();
    }

    private function changeState()
    {
        $this->controller->updateState();
    }

    private function generateOutput()
    {
        $title = 'Login example'; // fixa till en dynamisk title
        // $header = 'Assignment 3';
        // $container = $this->controller->getCurrentView()->response();
        // $footer = $this->dtv->show();
        // $isLoggedIn = $this->controller->getIsLoggedIn();
        // $view = new \View\LayoutView($header, $container, $footer, $isLoggedIn);
        $body = $this->view->getBody();
        $pageView = new \View\HTMLPageView($title, $body);
        $pageView->echoHTML();
    }
}
