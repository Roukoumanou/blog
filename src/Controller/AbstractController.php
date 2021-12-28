<?php 
namespace App\Controller;

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
        $twig->addGlobal('app', $_SESSION);

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