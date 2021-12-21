<?php

require_once 'vendor/autoload.php';
require_once 'config/router.php';

$match = $router->match();

if (!\is_null($match) && !\is_bool($match)) {
    if (\is_callable($match['target'])) {
        call_user_func_array($match['target'], $match['params']);
    }else{
        $params = $match['params'];
    }
}else {
    include 'view/_partials/404.html.twig';
}