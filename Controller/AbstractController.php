<?php 
require_once '../config/router.php';

/**
 * @author Amidou Roukoumanou <roukoumanouamidou@gmail.com>
 */
abstract class AbstractController
{
    /**
     * Renvois la vue twig d'une page du site
     *
     * @param string $tmp
     * @param array $option
     */
    protected function render(string $tmp, $option = [])
    {
        $loader = new \Twig\Loader\FilesystemLoader('../view/');
        $twig = new \Twig\Environment($loader);

        echo $twig->render($tmp, $option);
    }

    /**
     * Permet de rediriger sur une page
     *
     * @param string $route
     */
    protected function redirect(string $route)
    {
        return header('Location:'.$route);
    }
}