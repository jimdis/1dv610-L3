<?php

namespace Controller;

abstract class Controller
{

    protected $view;
    protected $storage;

    public function __construct(\View\View $view, \Model\UserStorage $storage)
    {
        $this->view = $view;
        $this->storage = $storage;
    }

    public function getViewResponse(): string
    {
        return $this->view->response();
    }

    protected function getUsername(): string
    {
        return $this->view->getFormUsername();
    }

    protected function getPassword(): string
    {
        return $this->view->getFormPassword();
    }

    protected function userIsAuthenticated(): bool
    {
        return $this->storage->getUser()->getIsAuthenticated();
    }
}
