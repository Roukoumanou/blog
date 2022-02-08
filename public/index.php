<?php

require dirname(__DIR__).'/vendor/autoload.php';
require dirname(__DIR__).'/config/router.php';
use Dotenv\Dotenv;
use App\Controller\Exception\ExceptionController;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
$dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'DB_DRIVER', 'MAIL_USERNAME', 'MAIL_PASSWORD']);

$match = $router->match();

if (!\is_null($match) && !\is_bool($match)) {
    if (\is_callable($match['target'])) {
        call_user_func_array($match['target'], $match['params']);
    }else{
        $params = $match['params'];
    }
}else {
    (new ExceptionController())->error404();
}
