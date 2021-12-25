<?php

session_start();
require_once '../vendor/autoload.php';
require_once '../config/router.php';
require_once '../Controller/Exception/ExceptionController.php';

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