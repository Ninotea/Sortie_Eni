<?php

namespace App\Controller;

use App\Donnee\DonneeSorties;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\DonneeType;
use App\Form\LieuType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route ("/sortie",name="sortie_")
 */
class SortieController extends AbstractController
{
    // TODO: factoriser la récupération du currentUSer
    //  quand dalila ne travaille pas sur le Participant repository
    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request,
                            EntityManagerInterface $entityManager,
                            EtatRepository $etatRepository,
                            UserInterface $user,
                            ParticipantRepository $participantRepository)
    {
        $sortie = new Sortie();
        $formSortie = $this->createForm(SortieType::class, $sortie);
        $formSortie->handleRequest($request);

        $currentUser = $participantRepository->findOneBy(['email'=>$user->getUserIdentifier()]);

        $lieu = new Lieu();
        $formLieu = $this->createForm(LieuType::class,$lieu);
        $formLieu->handleRequest($request);

        if ($formSortie->isSubmitted() && $formSortie->isValid()) {

            $sortie->setLeCampus($currentUser->getLeCampus());
            $sortie->setOrganisateur($currentUser);

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
            'formLieu' => $formLieu->createView(),
            'currentUser' => $currentUser
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
                             Request $request): Response
    {
        $sortie = $sortieRepository->find($id);
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $action = $form->getClickedButton()->getName() ;

            return $this->redirectToRoute('sortie_changeEtatFactory',['action'=>$action,'idSortie'=>$id]);

        }

        return $this->render('sortie/modifier_sortie.html.twig',[
            'sortieForm' => $form->createView(),
            'sortieModif'=> $sortie
        ]);
    }



    /**
     * @Route("/ajout_lieu", name="lieu")
     */
    public function ajouterLieu(Request $request,
                                EntityManagerInterface $entityManager): Response
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



    /**
     * @Route("/acceuil", name="acceuil")
     */
    public function afficherSortie(SortieRepository $sortieRepository,
                                   Request $request,
                                   UserInterface $user,
                                   ParticipantRepository $participantRepository):Response
    {
        $donnees = new DonneeSorties();
        $currentUser = $participantRepository->findOneBy(['email'=>$user->getUserIdentifier()]);
        //gestion page apginator ICI
        $formulaire = $this->createForm(DonneeType::class,$donnees);
        $formulaire->handleRequest($request);
        //$donnees est hydratée
        $sorties = $sortieRepository->rechercheSortie($donnees);
        return $this->render('main/index.html.twig',[
            'sorties'=>$sorties,
            'formulaire'=>$formulaire->createView(),
            'currentUser'=>$currentUser
        ]);
    }



    /**
     * @Route("/{action}/{idSortie}/{idUser}", name="inscriptionEtAction")
     */
    public function inscriptionEtAction(ParticipantRepository $participantRepository,
                                        SortieRepository $sortieRepository,
                                        int $idSortie,
                                        int $idUser,
                                        String $action) :Response
    {
        $sortie = $sortieRepository->find($idSortie);
        $participant = $participantRepository->find($idUser);

        switch ($action)
        {
            case "inscrire" :
                $sortie->addParticipant($participant);
                break;
            case "desinscrire":
                $sortie->removeParticipant($participant);
                break;
            case "modifier":
                return $this->redirectToRoute('sortie_modifier',['id'=>$idSortie]);
            case "annuler":
            case "publier":
                return $this->redirectToRoute('sortie_changeEtatFactory',['action'=>$action,'idSortie'=>$idSortie]);
        }
        return $this->redirectToRoute('sortie_acceuil');
    }



    /**
     * @Route("/{action}/{idSortie}", name="changeEtatFactory")
     */
    public function changeEtatFactory(String $action,
                                      int $idSortie,
                                      EntityManagerInterface $entityManager,
                                      SortieRepository $sortieRepository,
                                      EtatRepository $etatRepository):Response
    {
        $sortie = $sortieRepository->find($idSortie);
        $messageFlash = "L'action n'a pas pu etre réalisée.";
        $redirect = "Redirection standard vers l'acceuil";

        switch ($action)
        {
            case "modifier":
                $redirect = "modifier";
                break;

            case "annuler":
                $libelleEtat = 'Annulée';
                $messageFlash = 'Sortie annulée.';
                // TODO: gerer les commentaires d'annulation
                break;

            case "publier":
                $redirect = "detail";
                $libelleEtat = 'Ouverte';
                $messageFlash = 'Sortie publiée !';
                break;

            case "supprimer" :
                $entityManager->remove($sortie);
                $entityManager->flush();
                $this->addFlash('success','Sortie supprimer !');
                break;

            case "enregistrer" :
                $redirect = "detail";
                $libelleEtat = 'Créée';
                $messageFlash = 'Sortie enregistrée !';
                break;

        }
        /*
         * Traitement de l'état suivant les données recueillies
         */
        if(!empty($libelleEtat))
        {
            $etat = $etatRepository->findOneBy(['libelle'=>$libelleEtat]);
            $sortie->setUnEtat($etat);
            $entityManager->persist($sortie);
            $entityManager->flush();

        }
        $this->addFlash('success',$messageFlash);

        if($redirect === "detail"){
            return $this->redirectToRoute('sortie_detail', ['id'=>$idSortie]);
        }elseif($redirect === "modifier"){
            return $this->redirectToRoute('sortie_modifier',['id'=>$idSortie]);
        } // autre cas
            return $this->redirectToRoute('sortie_acceuil');

    }

}
