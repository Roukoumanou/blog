<?php

namespace App\Controller;

use Twig\Environment;
use App\Models\Manager;
use Twig\Loader\FilesystemLoader;

/**
 * @author Amidou Roukoumanou <roukoumanouamidou@gmail.com>
 */
abstract class AbstractController extends Manager
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

        $_SESSION['flashes'] = [];
    }

    protected function getUser()
    {
        if (isset($_SESSION['user'])) {
            return $_SESSION['user'];
        }

        return null;
    }

    /**
     * Permet de rajouter une petite notification
     *
     * @param string $label
     * @param string $message
     * @return void
     */
    protected function addFlash($label, $message): void
    {
        $_SESSION['flashes'] = [
            $label => [$message]
        ];
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

    protected function getDB()
    {
        return Manager::getInstance()->getEm();
    }
}
