<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/acceuil", name="main_acceuil")
     */
    public function index(): Response
    {
        return $this->redirectToRoute('sortie_acceuil');
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
    public function ville(Request $request,
                          EntityManagerInterface $entityManager,
                          VilleRepository $villeRepository): Response
    {
        $ville = new Ville();
        $listeVille = $villeRepository->findAll();
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($ville);
            $entityManager->flush();
            return $this->redirectToRoute('main_ville');
        }

        return $this->render('administration/ville.html.twig',[
            'villeForm'=> $form->createView(),
            'liste_ville'=> $listeVille
        ]);
    }
    /**
     * @Route("/campus", name="main_campus")
     */
    public function campus(): Response
    {
        return $this->render('administration/campus.html.twig');
    }
}
