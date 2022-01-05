<?php

namespace App\Controller;

use App\Controller\AbstractController;
/**
 * @author Amidou Roukoumanou <roukoumanouamidou@gmail.com>
 * Affiche la page d'accueil du site
 */
class HomeController extends AbstractController
{
    public function index()
    {
        return $this->render('home.html.twig', ['name' => "Roukoumanou"]);
    }
}
