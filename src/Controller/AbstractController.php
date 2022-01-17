<?php 
namespace App\Controller;

use Slim\Csrf\Guard;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

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
        $loader = new FilesystemLoader('../view/');
        $twig = new Environment($loader);
        $twig->addGlobal('app', $_SESSION);

        echo $twig->render($tmp, $option);
    }

    protected function getUser()
    {
        if (isset($_SESSION['user'])) {
            return $_SESSION['user'];
        }

        return null;
    }

    /**
     * Permet de rediriger sur une page
     *
     * @param string $route
     */
    protected function redirect(string $route)
    {
        header('Location:'.$route);
    }

    /**
     * Vérify l'authenticité du token csrf
     *
     * @param array $post
     * @return boolean
     */    
    protected function csrfVerify(array $post): bool
    {
        if ($post['_token'] === $this->getUser()['token']) {
            return true;
        }

        return false;
    }
}
