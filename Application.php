<?php

require_once 'requires.php';

class Application
{
    private $storage;
    private $view;
    private $controller;


    public function __construct()
    {
        $routes = new \Model\Routes();
        $controller = $routes->getController();
        $view = $routes->getView();
        $this->storage = new \Model\UserStorage();
        $this->view = new $view($this->storage);
        $this->controller = new $controller($this->view, $this->storage);
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
        if ($this->controller->redirectToLogin()) {
            $this->view = new \View\LoginView($this->storage);
            $this->controller = new \Controller\LoginController($this->view, $this->storage);
            $this->view->setMessage('Registered new user.');
        }
    }

    private function generateOutput()
    {
        $title = '1337 PHP Software';
        $style = file_get_contents('styles/styles.css');
        $statusBar = new \View\StatusBar($this->storage);
        $footer = new \View\DateTimeView();
        $container = '
                <div class="container">' . $this->controller->getViewHTML() . '
                </div>
                ';
        $body = $statusBar->show() . $container . $footer->show();
        $pageView = new \View\HTMLPageView($title, $style, $body);
        $pageView->echoHTML();
    }

    private function handleException(\Exception $e)
    {
        try {
            $this->view->setMessage($e->getMessage());
            $this->generateOutput();
        } catch (\Exception $e) {
            echo 'Oops.. something went wrong.. Sorry! Error info: ' . $e->getMessage() . '
            <br/><a href=".">Go back!</a>';
        }
    }
}
