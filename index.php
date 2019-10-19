<?php
require_once('Config.php');
require_once("Application.php");

Config::setErrorReporting();

$app = new Application();
$app->run();
