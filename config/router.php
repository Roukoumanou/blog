<?php 

require_once 'Controller/HomeController.php';

$router = new AltoRouter();

// map homepage
$router->map('GET', '/', function() {
    $home = new HomeController();
    $home->index();
}, 'home');