<?php

namespace App\Controller;

use Symfony\Component\Dotenv\Dotenv;
use App\Controller\AbstractController;
/**
 * @author Amidou Roukoumanou <roukoumanouamidou@gmail.com>
 * Affiche la page d'accueil du site
 */
class HomeController extends AbstractController
{
    public function index()
    {
        return $this->render('home.html.twig', [
            'title' => 'Accueil',
            'myName' => 'Amidou Abdou Roukoumanou',
            'description' => 'Consultant Expert Developpeur Web'
        ]);
    }
}
