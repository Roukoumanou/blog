<?php 

namespace App\Controller\Administrator;

use App\Controller\AbstractController;

/**
 * @author Amidou Roukoumanou <roukoumanouamidou@gmail.com>
 * Affiche la page d'accueil de l'administrateur
 */
class AdminHomeController extends AbstractController
{
    public function adminIndex()
    {
        return $this->render('admin/home.html.twig', ['title' => 'Admin DashBoard']);
    }
}
