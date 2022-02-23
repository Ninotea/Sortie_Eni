<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\LieuType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route ("/sortie",name="sortie_")
 */
class SortieController extends AbstractController
{
    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request,
                           EntityManagerInterface $entityManager,
                           EtatRepository $etatRepository)
    {
        $sortie = new Sortie();
        $formSortie = $this->createForm(SortieType::class, $sortie);
        $formSortie->handleRequest($request);

        $lieu = new Lieu();
        $formLieu = $this->createForm(LieuType::class,$lieu);
        $formLieu->handleRequest($request);

        if ($formSortie->isSubmitted() && $formSortie->isValid()) {

            $button = $formSortie->getClickedButton()->getName() ;
            switch ($button)
            {
                case "publier" :
                    $etat = $etatRepository->findOneBy(['libelle'=>'Ouverte']);
                    $sortie->setUnEtat($etat);
                    $this->addFlash('success','Sortie publier !');
                    break;
                case "enregistrer" :
                    $etat = $etatRepository->findOneBy(['libelle'=>'Créée']);
                    $sortie->setUnEtat($etat);
                    $this->addFlash('success','Sortie enregistrée !');
                    break;
                case "ajouterLieu" :
                    return $this->redirectToRoute('sortie_lieu',[
                        'donneesSortie'=>$sortie
                    ]);
            }

            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('sortie_detail', [
                'id'=>$sortie->getId()
            ]);
        }

        return $this->render('sortie/ajout_sortie.html.twig',[
            'formSortie'=> $formSortie->createView(),
            'formLieu' => $formLieu->createView()
        ]);
    }
    /**
     * @Route("/detail/{id}", name="detail")
     */
    public function detail(int $id, SortieRepository $sortieRepository): Response
    {
        $sortie = $sortieRepository->find($id);
        return $this->render('sortie/detail_sortie.html.twig',[
            'sortieAffichee' => $sortie
        ]);
    }
    /**
     * @Route("/modifier/{id}", name="modifier")
     */
    public function modifier(int $id,
                             SortieRepository $sortieRepository,
                             EntityManagerInterface $entityManager,
                             EtatRepository $etatRepository,
                             Request $request): Response
    {
        $sortie = $sortieRepository->find($id);
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $button = $form->getClickedButton()->getName() ;
            switch ($button)
            {
                case "annuler" :
                    $etat = $etatRepository->findOneBy(['libelle'=>'Annulée']);
                    $sortie->setUnEtat($etat);
                    break;
                case "publier" :
                    $etat = $etatRepository->findOneBy(['libelle'=>'Ouverte']);
                    $sortie->setUnEtat($etat);
                    $this->addFlash('success','Sortie publier !');
                    break;
                case "enregistrer" :
                    $etat = $etatRepository->findOneBy(['libelle'=>'Créée']);
                    $sortie->setUnEtat($etat);
                    $this->addFlash('success','Sortie enregistrée !');
                    break;
                case "supprimer" :
                    $this->addFlash('success','Sortie supprimer !');
                    $entityManager->remove($sortie);
                    $entityManager->flush();
                    return $this->redirectToRoute('main_acceuil');

            }
            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('sortie_detail', [
                'id'=>$sortie->getId()
            ]);
        }

        return $this->render('sortie/modifier_sortie.html.twig',[
            'sortieForm' => $form->createView(),
            'sortieModif'=> $sortie
        ]);
    }

    /**
     * @Route("/ajout_lieu", name="lieu")
     */
    public function ajouterLieu(Request $request,EntityManagerInterface $entityManager): Response
    {
        $lieu = new Lieu();
        $formLieu = $this->createForm(LieuType::class,$lieu);
        $formLieu->handleRequest($request);

        if ($formLieu->isSubmitted() && $formLieu->isValid()) {

            $entityManager->persist($lieu);
            $entityManager->flush();

            return $this->redirectToRoute('sortie_create');
        }

        return $this->render('sortie/ajout_lieu.html.twig',[
            'formLieu' => $formLieu->createView()
        ]);
    }
}
