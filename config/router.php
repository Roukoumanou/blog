<?php
/**
 * @author Amidou Roukoumanou <roukoumanouamidou@gmail.com>
 * Cette page sert a de router pour tout le site
 * On n'y retrouve les routes de l'application
 */
require_once '../vendor/altorouter/altorouter/AltoRouter.php';
require_once '../Controller/HomeController.php';
require_once '../Controller/Administrator/AdminHomeController.php';
require_once '../Controller/UsersController.php';

$router = new AltoRouter();

// map homepage
$router->map('GET', '/', function() {
    (new HomeController())->index();
}, 'home');

// map admin homepage
$router->map('GET', '/admin', function() {
    (new AdminHomeController())->adminIndex();
}, 'admin_home');

// map registerpage
$router->map('GET|POST', '/register', function() {
    (new UsersController())->register();
}, 'register');

// map user Connect Page
$router->map('GET|POST', '/connexion', function() {
    (new UsersController())->connexion();
}, 'connexion');