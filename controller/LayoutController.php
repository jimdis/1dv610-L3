<?php

namespace Controller;

class LayoutController extends Controller
{

    private static $registerQuery = 'register';
    private $header = 'Assignment 3';
    private $loginController;
    private $registerController;

    public function __construct(\View\LayoutView $view)
    {
        parent::__construct($view);
        $dtv = new \View\DateTimeView;
        $this->view->setFooter($dtv->show());
        $this->loginController = new \Controller\LoginController(new \View\LoginView());
        $this->registerController = new \Controller\RegisterController(new \View\RegisterView());
    }

    public function updateState(): void
    {
        $this->loginController->updateState();
        $this->registerController->updateState();
        $this->updateView();
    }

    private function updateView(): void
    {
        $this->view->setHeader($this->header);
        $this->view->setIsLoggedIn($this->loginController->getIsLoggedIn());
        $this->selectContainer();
    }

    private function selectContainer(): void
    {
        $query = $this->view->getQuery();
        $successfulRegister = $this->registerController->getRegisterSuccess();

        if ($successfulRegister) {
            $user = $this->registerController->getUser();
            $this->loginController->updateMessage('Registered new user.');
            $this->loginController->updateLoginUsername($user->getUsername());
        }
        if ($query == self::$registerQuery && !$successfulRegister) {
            $this->view->setContainer($this->registerController->getViewResponse());
        } else {
            $this->view->setContainer($this->loginController->getViewResponse());
        }
    }
}
