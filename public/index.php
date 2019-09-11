<?php
use PVAN\HomeController;

// init composer autoloader
require_once dirname(__DIR__).'/vendor/autoload.php';

// instantiate Home controller and render default page
$controller = new HomeController;
echo $controller->home($_REQUEST);
