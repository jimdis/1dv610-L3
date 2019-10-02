<?php

require_once 'view/LoginView.php';
require_once 'view/RegisterView.php';
require_once 'view/DateTimeView.php';
require_once 'view/LayoutView.php';
require_once 'view/HTMLPageView.php';
require_once 'controller/Controller.php';
require_once 'model/UserStorage.php';
require_once 'model/User.php';

class Application
{
    private $storage;
    private $controller;
    private $dtv;
    private $loginView;
    // private $registerView;

    public function __construct()
    {
        $this->dtv = new \View\DateTimeView();
        $this->storage = new \Model\UserStorage();
        // $this->user = $this->storage->loadUser();
        $this->loginView = new \View\LoginView($this->storage);
        // $this->registerView = new \View\RegisterView($this->user);
        $this->controller = new \Controller\Controller($this->storage, $this->loginView);
    }

    public function run()
    {
        $this->changeState();
        $this->generateOutput();
    }

    private function changeState()
    {
        $this->controller->updateState();
        // $this->storage->saveUser($this->user);
    }

    private function generateOutput()
    {
        $title = 'Login example'; // fixa till en dynamisk title
        $header = 'Assignment 3';
        $container = $this->controller->getCurrentView()->response();
        $footer = $this->dtv->show();
        $isLoggedIn = $this->controller->getIsLoggedIn();
        $view = new \View\LayoutView($header, $container, $footer, $isLoggedIn);
        $body = $view->getBody();
        $pageView = new \View\HTMLPageView($title, $body);
        $pageView->echoHTML();
    }
}
