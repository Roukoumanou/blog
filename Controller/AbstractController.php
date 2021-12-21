<?php 

abstract class AbstractController
{
    protected function render(string $tmp, $option = [])
    {
        $loader = new \Twig\Loader\FilesystemLoader('view/');
        $twig = new \Twig\Environment($loader);

        echo $twig->render($tmp, $option);
    }
}