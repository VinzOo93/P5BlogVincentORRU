<?php
require "vendor/autoload.php";
use App\Controller\HomeController;
use App\Controller\CrudUserController;

    $router = new App\Router\Router($_GET['url']);

    $router->get('/home', function () {
        echo HomeController::showHome();
    });
    $router->get('/createuser', function (){
        echo CrudUserController::showForm();
    });
    $router->post('/addUser', function () {
        try {
            CrudUserController::addUser($_POST['name'], $_POST['firstName'], $_POST['email'], $_POST['password']);
        } catch (Exception $e){
            echo 'ereur mysql';
        }
    });

try {
    $router->run();
} catch (\App\Router\RouterException $e) {
    echo 'Error Routing:' .$e;
}











