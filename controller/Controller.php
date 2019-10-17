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
}
