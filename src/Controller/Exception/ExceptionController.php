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
        return $this->render('exception/404.html.twig', [
            'title' => '<div class="alert alert-danger text-center" role="alert">
                    Impossible de trouver cette page <br > 
                    <a class="btn btn-primary" href="/">Retour à la page d\'accueil</a>
                    </div>']);
    }

    /**
     * Affiche la page erreur 500 erreur serveur
     */
    public function error500($message)
    {
        return $this->render('exception/500.html.twig', [
            'title' => '<div class="alert alert-danger text-center" role="alert">
                    '.$message.' <br > 
                    <a class="btn btn-primary mt-4" href="/">Retour à la page d\'accueil</a>
                    </div>'
        ]);
    }
}
