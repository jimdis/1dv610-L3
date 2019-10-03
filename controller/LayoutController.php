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
        // $this->registerController->updateState();
        $this->updateView();
    }

    public function updateView(): void
    {
        $this->view->setHeader($this->header);

        if ($this->view->getQuery() == self::$registerQuery) {
            $this->view->setContainer($this->registerController->getViewResponse());
        } else {
            $this->view->setContainer($this->loginController->getViewResponse());
        }
    }
}
