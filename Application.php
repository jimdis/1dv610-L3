<?php

require_once 'Config.php';
require_once 'view/partials/MessageTable.php';
require_once 'view/View.php';
require_once 'view/LoginView.php';
require_once 'view/RegisterView.php';
require_once 'view/DateTimeView.php';
require_once 'view/MessageView.php';
require_once 'view/LayoutView.php';
require_once 'view/HTMLPageView.php';
require_once 'controller/Controller.php';
require_once 'controller/LayoutController.php';
require_once 'controller/LoginController.php';
require_once 'controller/RegisterController.php';
require_once 'controller/MessageController.php';
require_once 'model/SanitizeInput.php';
require_once 'model/Credentials.php';
require_once 'model/Token.php';
require_once 'model/UserStorage.php';
require_once 'model/Database.php';
require_once 'model/UserDAL.php';
require_once 'model/Username.php';
require_once 'model/User.php';
require_once 'model/MessageDAL.php';
require_once 'model/MessageStorage.php';
require_once 'model/Message.php';


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
        try {
            $this->changeState();
            $this->generateOutput();
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    private function changeState()
    {
        $this->controller->updateState();
    }

    private function generateOutput()
    {
        $title = 'Login example'; // TODO: fixa till en dynamisk title
        $body = $this->view->getBody();
        $pageView = new \View\HTMLPageView($title, $body);
        $pageView->echoHTML();
    }

    private function handleException(\Exception $e)
    {
        $title = 'An error occured';
        $body = '
                <p>Ooops.. An error occured. Sorry! Error info: ' . $e->getMessage() . '</p>
                <a href="?">Go back!</a>';
        $pageView = new \View\HTMLPageView($title, $body);
        $pageView->echoHTML();
    }
}
