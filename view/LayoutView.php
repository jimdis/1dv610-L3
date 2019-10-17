<?php

namespace View;

class LayoutView extends View
{
    private $header = '';
    private $container = '';
    private $footer = '';

    public function getQuery(): string
    {
        $query = array_keys($_GET);
        return $query[0] ?? '';
    }

    public function setHeader(string $header): void
    {
        $this->header = $header;
    }

    public function setContainer(string $container): void
    {
        $this->container = $container;
    }

    public function setFooter(string $footer): void
    {
        $this->footer = $footer;
    }

    public function getBody(): string
    {
        return '<h1>' . $this->header . '</h1>
                ' . $this->renderIsAuthenticated() . '
                <div class="container">
                ' . $this->container . '
                </div>
                ' . $this->footer . '';
    }

    private function renderIsAuthenticated(): string
    {
        if ($this->storage->getUserIsAuthenticated()) {
            return '<h2>Logged in</h2>';
        } else {
            return '<h2>Not logged in</h2>';
        }
    }
}
