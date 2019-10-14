<?php

namespace Controller;

abstract class Controller
{

    protected $view;
    // private $storage;

    public function __construct(\View\View $view)
    {
        $this->view = $view;
        // $this->storage = new \model\UserStorage;
    }

    public function getViewResponse(): string
    {
        return $this->view->response();
    }

    public function updateState(): void
    { }

    protected function getUsername(): string
    {
        return $this->view->getFormUsername();
    }

    protected function getPassword(): string
    {
        return $this->view->getFormPassword();
    }
}
