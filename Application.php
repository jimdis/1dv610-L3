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
    // private $user;
    private $loginView;
    // private $registerView;

    public function __construct()
    {
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
        $body = $this->controller->getCurrentView()->response(); // fixa så h1 kommer med.
        $title = 'Login example'; // fixa till en dynamisk title
        $pageView = new \View\HTMLPageView($title, $body);
        $pageView->echoHTML();
    }
}
