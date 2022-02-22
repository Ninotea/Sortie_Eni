<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
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
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $button = $form->getClickedButton()->getName() ;
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

            }
            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('sortie_detail', [
                'id'=>$sortie->getId()
            ]);
        }

        return $this->render('sortie/ajout_sortie.html.twig',[
            'sortieForm'=> $form->createView()
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

}
