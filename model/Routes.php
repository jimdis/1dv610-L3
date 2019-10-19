<?php

namespace Model;



class Routes
{
    static $register = 'register';
    static $messages = 'messages';
    private $route;

    public function __construct()
    {
        $query = [];
        parse_str($_SERVER['QUERY_STRING'], $query);
        $this->route = array_keys($query)[0] ?? '';
    }

    public  function getController()
    {

        if ($this->route == self::$register) {
            return \Controller\RegisterController::class;
        } else if ($this->route == self::$messages) {
            return \Controller\MessageController::class;
        } else return \Controller\LoginController::class;
    }

    public function getView()
    {
        if ($this->route == self::$register) {
            return \View\RegisterView::class;
        } else if ($this->route == self::$messages) {
            return \View\MessageView::class;
        } else return \View\LoginView::class;
    }
}
