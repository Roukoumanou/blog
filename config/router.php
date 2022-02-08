<?php

require_once '../vendor/autoload.php';
require_once '../vendor/altorouter/altorouter/AltoRouter.php';
use App\Entity\Users;
use App\Controller\HomeController;
use App\Controller\UsersController;
use App\Controller\ContactController;
use App\Controller\BlogsPostsController;
use App\Controller\Exception\ExceptionController;
use App\Controller\Administrator\AdminHomeController;
use App\Controller\Administrator\AdminPostsController;
use App\Controller\Administrator\AdminCommentsController;

/**
 * @author Amidou Roukoumanou <roukoumanouamidou@gmail.com>
 * Cette page sert de routeur pour tout le site
 * On n'y retrouve les routes de l'application
 */

try{

    // Start session
    session_start(['cookie_lifetime' => 86400]);
    if (!isset($_SESSION['user'])) {
        $_SESSION['user'] = [
            'token' => "blog_token_annonymously",
            'is_connected' => false];
        
    }
} catch (Exception $e) {
    return (new ExceptionController())->error500($e->getMessage());
}

$router = new AltoRouter();

// map homepage
$router->map('GET', '/', function() {
    (new HomeController())->index();
}, 'home');

// map registerpage
$router->map('GET|POST', '/registration', function() {
    (new UsersController())->registration();
}, 'registration');

$router->map('GET', '/posts[i:id]?', function(int $id = 1){
    (new BlogsPostsController())->posts($id);
}, 'list-posts');

$router->map('GET|POST', '/post-[i:id]', function($id){
    (new BlogsPostsController())->showPost(htmlspecialchars($id));
}, 'view_post');

// map user Connect Page
$router->map('GET|POST', '/login', function() {
    (new UsersController())->login();
}, 'login');

// map admin Connect Page
$router->map('GET|POST', '/admin-login', function() {
    (new UsersController())->adminLogin();
}, 'admin_login');

// map homepage
$router->map('GET|POST', '/contact', function() {
    (new ContactController())->contactMe();
}, 'contact');

// map user DesConnect Page
$router->map('GET', '/logout', function() {
    (new UsersController())->logout();
}, 'logout');

if ($_SESSION['user']['is_connected'] === true) {
    $user = $_SESSION['user'];

    // map user update account Page
    $router->map('GET', '/profil', function(){
        (new UsersController())->profil();
    }, 'profil');
    
    // map user update account Page
    $router->map('GET|POST', '/account-update', function(){
        (new UsersController())->updateAccount();
    }, 'account_update');

    $router->map('GET|POST', '/password-update', function(){
        (new UsersController())->updatePassword();
    }, 'password_update');

    if ($user['role'] == Users::ROLE_ADMIN) {
        // map admin homepage
        $router->map('GET', '/admin', function() {
            (new AdminHomeController())->adminIndex();
        }, 'admin_home');

        $router->map('GET|POST', '/admin-new-post', function(){
            (new AdminPostsController())->newPost();
        }, 'new_post');

        $router->map('GET|POST', '/admin-update-post-[i:id]', function($id){
            (new AdminPostsController())->updatePost(htmlspecialchars($id));
        }, 'update_post');

        $router->map('POST', '/admin-delete-post-[i:id]', function($id){
            (new AdminPostsController())->deletePost(htmlspecialchars($id));
        }, 'admin_delete_post');

        $router->map('GET', '/admin-posts[i:id]?', function(int $id = 1){
            (new AdminPostsController())->postList($id);
        }, 'posts_list');

        $router->map('GET', '/admin-comments[i:id]?', function(int $id = 1){
            (new AdminCommentsController())->comments($id);
        }, 'comments_list');

        $router->map('GET', '/admin-comment-[i:id]', function($id){
            (new AdminCommentsController())->comment(htmlspecialchars($id));
        }, 'admin_comment');

        $router->map('POST', '/admin-valided-comment-[i:id]', function($id){
            (new AdminCommentsController())->validedComment(htmlspecialchars($id));
        }, 'admin_valided_comment');
    }
}
