<?php
require_once 'Controller/AbstractController.php';

class HomeController extends AbstractController
{
    public function index()
    {
        return $this->render('home.html.twig', ['name' => "Roukoumanou"]);
    }
}