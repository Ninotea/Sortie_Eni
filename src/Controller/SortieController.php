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

            // Conversion de la durée en entier
            $timeH = date_format($sortie->getDureeH(),'H');
            $timeM = date_format($sortie->getDureeH(),'i');
            $sortie->setDuree((integer)$timeH*60 + (integer)$timeM);


            $button = $formSortie->getClickedButton()->getName() ;
            switch ($button)
            {
                case "publier" :
                    $etatNom = 'Ouverte';
                    $message = 'Sortie publier !';
                    break;
                case "enregistrer" :
                    $etatNom = 'Créée';
                    $message = 'Sortie enregistrée !';
                    break;
                case "ajouterLieu" :
                    return $this->redirectToRoute('sortie_lieu',[
                        'donneesSortie'=>$sortie
                    ]);
            }

            $this->addFlash('success',$message);
            $etat = $etatRepository->findOneBy(['libelle'=>$etatNom]);
            $sortie->setUnEtat($etat);
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
        $dureeH = $sortie->getDuree()/60;
        $dureeM = $sortie->getDuree()%60;
        $duree = "$dureeH:$dureeM";
        return $this->render('sortie/detail_sortie.html.twig',[
            'sortieAffichee' => $sortie,
            'duree'=>$duree
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
            switch ($action)
            {
                case "enregistrer":
                case "publier":
                    $route = "detail";
                    break;
                case "annuler":
                case "supprimer":
                    $route = "acceuil";
                    break;

            }
            return $this->redirectToRoute('sortie_changeEtatFactory',['action'=>$action,'idSortie'=>$id,'route'=>$route]);

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
        //$this->actualiserEtat(); TODO: actualiser les etats dans la BDD en fonction de la date du jour
        $donnees = new DonneeSorties();
        $currentUser = $participantRepository->findOneBy(['email'=>$user->getUserIdentifier()]);
        //gestion page apginator ICI
        //gestion DATE
        $formulaire = $this->createForm(DonneeType::class,$donnees);
        $formulaire->handleRequest($request);
        $submitted = false;
        if($formulaire->isSubmitted()) $submitted = true;
        //$donnees est hydratée
        $sorties = $sortieRepository->rechercheSortie($donnees,$currentUser);
        return $this->render('main/index.html.twig',[
            'sorties'=>$sorties,
            'formulaire'=>$formulaire->createView(),
            'currentUser'=>$currentUser,
            'submitted'=>$submitted
        ]);
    }



    /**
     * @Route("/{action}/{idSortie}/{idUser}/{route}", name="inscriptionEtAction")
     */
    public function inscriptionEtAction(ParticipantRepository $participantRepository,
                                        SortieRepository $sortieRepository,
                                        EntityManagerInterface $entityManager,
                                        int $idSortie,
                                        int $idUser,
                                        String $route,
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
                return $this->redirectToRoute('sortie_changeEtatFactory',['action'=>$action,'idSortie'=>$idSortie,'route'=>$route]);
        }

        $entityManager->persist($sortie);
        $entityManager->flush();

        return $this->redirectToRoute('sortie_acceuil');
    }



    /**
     * @Route("/{action}/{idSortie}/{route}", name="changeEtatFactory")
     */
    public function changeEtatFactory(String $action,
                                      String $route,
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
                $redirect = $route;
                $libelleEtat = 'Annulée';
                $messageFlash = 'Sortie annulée.';
                // TODO: gerer les commentaires d'annulation
                break;

            case "publier":
                $redirect = $route;
                $libelleEtat = 'Ouverte';
                $messageFlash = 'Sortie publiée !';
                break;

            case "supprimer" :
                $redirect = $route;
                $entityManager->remove($sortie);
                $entityManager->flush();
                $this->addFlash('success','Sortie supprimer !');
                break;

            case "enregistrer" :
                $redirect = $route;
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
        }elseif (($redirect === "acceuil")){
            return $this->redirectToRoute('sortie_acceuil');
        }
        $this->addFlash('warning','Erreur de routage !');
        return $this->redirectToRoute('sortie_acceuil');
    }

}
