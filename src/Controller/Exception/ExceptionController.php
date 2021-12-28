<?php

namespace App\Controller\Exception;

use App\Controller\AbstractController;

/**
 * @author Amidou Roukoumanou <roukoumanouamidou@gmail.com>
 * Cette classe gère les exceptions
 */
class ExceptionController extends AbstractController
{

    /**
     * Affiche la page erreur 404
     */
    public function error404()
    {
        return $this->render('exception/404.html.twig', ['title' => 'Impossible de trouver cette page']);
    }

    /**
     * Affiche la page erreur 500 erreur serveur
     */
    public function error500($message)
    {
        return $this->render('exception/500.html.twig', ['title' => $message]);
    }
}