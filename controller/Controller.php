<?php

namespace Controller;

abstract class Controller
{

    protected $view;

    public function __construct(\View\View $view)
    {
        $this->view = $view;
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
}
