<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
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
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $button = $form->getClickedButton()->getName() ;
            switch ($button)
            {
                case "annuler" :
                    return $this->redirectToRoute('main_acceuil');
                case "publier" :
                    //TODO $sortie->setEtat activé
                    $this->addFlash('success','Sortie publier !');

                    break;
                case "enregistrer" :
                    //TODO $sortie->setEtat pas activé
                    $this->addFlash('success','Sortie enregistrée !');
                    break;
                case "supprimer" :
                    //TODO supprimer sortie
                    $this->addFlash('success','Sortie supprimer !');
                    return $this->redirectToRoute('main_acceuil');

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
    public function modifier(int $id, SortieRepository $sortieRepository): Response
    {
        $sortie = $sortieRepository->find($id);
        return $this->render('sortie/modifier_sortie.html.twig',[
            'sortieModif' => $sortie
        ]);
    }

}
