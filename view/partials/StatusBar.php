<?php

namespace View;

class StatusBar extends View
{
    private static $header = 'Assignment 3';

    public function show(): string
    {
        $isLoggedIn = $this->storage->getUserIsAuthenticated();
        $loggedInMessage = $isLoggedIn ? '<h2>Logged in</h2>' : '<h2>Not logged in</h2>';

        return '<h1>' . self::$header . '</h1>
                ' . $loggedInMessage;
    }
}
