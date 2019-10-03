<?php

namespace Controller;

class Controller
{

    private $view;

    public function __construct(\View\View $view)
    {
        $this->view = $view;
    }

    public function getView(): string
    {
        return $this->view->response();
    }
}
