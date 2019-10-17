<?php

namespace Controller;

class LayoutController extends Controller
{

    private static $registerQuery = 'register';
    private static $messagesQuery = 'messages';
    private $header = 'Assignment 3';
    private $lc;
    private $rc;
    private $mc;
    private $currentController;

    public function __construct(\View\LayoutView $view, \Model\UserStorage $storage)
    {
        parent::__construct($view, $storage);
        $dtv = new \View\DateTimeView;
        $this->view->setFooter($dtv->show());
        $loginView = new \View\LoginView($this->storage);
        $registerView = new \View\RegisterView($this->storage);
        $messageView = new \View\MessageView($this->storage);
        $this->lc = new \Controller\LoginController($loginView, $this->storage);
        $this->rc = new \Controller\RegisterController($registerView, $this->storage);
        $this->mc = new \Controller\MessageController($messageView, $this->storage);
    }

    public function updateState(): void
    {
        $this->view->setHeader($this->header);
        $this->selectController();
        $this->currentController->updateState();
        $this->updateView();
    }

    private function selectController(): void
    {
        $query = $this->view->getQuery();
        if ($query == self::$registerQuery) {
            $this->currentController = $this->rc;
        } else if ($query == self::$messagesQuery) {
            $this->currentController = $this->mc;
        } else $this->currentController = $this->lc;
    }

    private function updateView(): void
    {
        if ($this->rc->getRedirect()) {
            $this->currentController = $this->lc;
            $this->lc->updateMessage('Registered new user.');
        }
        $response = $this->currentController->getViewResponse();
        $this->view->setContainer($response);
    }
}
