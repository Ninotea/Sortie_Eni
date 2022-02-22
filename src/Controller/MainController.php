<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/acceuil", name="main_acceuil")
     */
    public function index(): Response
    {
        return $this->render('main/index.html.twig');
    }
    /**
     * @Route("/profil", name="main_profil")
     */
    public function profil(): Response
    {
        return $this->render('participant/profil.html.twig');
    }
    /**
     * @Route("/ville", name="main_ville")
     */
    public function ville(): Response
    {
        return $this->render('administration/ville.html.twig');
    }
    /**
     * @Route("/campus", name="main_campus")
     */
    public function campus(): Response
    {
        return $this->render('administration/campus.html.twig');
    }
}
