<?php

namespace Controller;

abstract class Controller
{

    protected $view;
    protected $storage;
    protected $redirect = false;

    public function __construct(\View\View $view, \Model\UserStorage $storage)
    {
        $this->view = $view;
        $this->storage = $storage;
    }

    public function getViewResponse(): string
    {
        return $this->view->response();
    }

    public function redirectToLogin(): bool
    {
        return $this->redirect;
    }
}
