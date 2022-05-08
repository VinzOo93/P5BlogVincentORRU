<?php
require "vendor/autoload.php";

use App\Router\Router;

try {
    $router = new Router($_GET['url']);
    $router->run();
} catch (\App\Router\RouterException $e) {
    echo 'Error Routing:' . $e;
}











