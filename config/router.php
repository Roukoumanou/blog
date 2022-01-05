<?php

require_once '../vendor/autoload.php';
require_once '../vendor/altorouter/altorouter/AltoRouter.php';
use App\Controller\HomeController;
use App\Controller\UsersController;
use App\Controller\Administrator\AdminHomeController;
use App\Entity\Users;

/**
 * @author Amidou Roukoumanou <roukoumanouamidou@gmail.com>
 * Cette page sert a de router pour tout le site
 * On n'y retrouve les routes de l'application
 */

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
$router->map('GET|POST', '/registration', function() {
    (new UsersController())->registration();
}, 'registration');

// map user Connect Page
$router->map('GET|POST', '/login', function() {
    (new UsersController())->login();
}, 'login');

// map user DesConnect Page
$router->map('GET', '/logout', function() {
    (new UsersController())->logout();
}, 'logout');

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    // map user update account Page
    $router->map('GET|POST', '/account-update', function(){
        (new UsersController())->updateAccount();
    }, 'account_update');

    $router->map('GET|POST', '/password-update', function(){
        (new UsersController())->updatePassword();
    }, 'password_update');
}
