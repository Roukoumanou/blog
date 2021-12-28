<?php
session_start();
require dirname(__DIR__).'/vendor/autoload.php';
require dirname(__DIR__).'/config/router.php';
use App\Controller\Exception\ExceptionController;
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