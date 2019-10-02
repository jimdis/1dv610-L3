<?php

namespace View;

class LayoutView
{
    private $header;
    private $container;
    private $footer;
    private $isLoggedIn;

    //gör smartare  ta in view på nåt sätt
    public function __construct(string $header, string $container, string $footer, bool $isLoggedIn)
    {
        $this->header = $header;
        $this->container = $container;
        $this->footer = $footer;
        $this->isLoggedIn = $isLoggedIn;
    }

    public function getBody(): string
    {
        return '<h1>' . $this->header . '</h1>
                ' . $this->renderIsLoggedIn() . '
                <div class="container">
                ' . $this->container . '
                </div>
                ' . $this->footer . '';
    }

    private function renderIsLoggedIn(): string
    {
        if ($this->isLoggedIn) {
            return '<h2>Logged in</h2>';
        } else {
            return '<h2>Not logged in</h2>';
        }
    }
}
