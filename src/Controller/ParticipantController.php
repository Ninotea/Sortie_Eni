<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ProfileType;
use App\Form\RegistrationFormType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ParticipantController extends AbstractController
{

    /**
     * @Route("/profil/{id}", name="participant_profil_id")
     * @param int $id
     * @param ParticipantRepository $participantRepository
     * @return Response
     */
    public function afficherProfilId(int $id, ParticipantRepository $participantRepository): Response
    {
        $participant = $participantRepository->find($id);
        return $this->render('participant/profil.html.twig', [
            'participant' => $participant
        ]);
    }


    /**
     * @Route("/profil", name="participant_profil")
     */
    public function afficherProfil(): Response
    {
        return $this->render('participant/profil.html.twig');
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('main_acceuil');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/modification/{id}", name="profil_modifier")
     */
    public function modifierProfil(int                    $id,
                                  EntityManagerInterface $entityManager,
                                  ParticipantRepository  $participantRepository,
                                  Request                $request): Response
    {
        $participant = $participantRepository->find($id);
        $form = $this->createForm(RegistrationFormType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($participant);
            $entityManager->flush();

            //TODO persist et flush le participant "MODIFIER"
            return $this->redirectToRoute('participant_profil_id', [
                'id' => $participant->getId()
            ]);
        }
        return $this->render('participant/modificationProfil.html.twig',
            ['form' => $form->createView()]);

    }

    /*/**
     * @Route("/{action}/{idUtilisateur}", name="modification_profil")
     */
    /*public function modifierProfil (int $id,
                                    participantRepository $participantRepository,
                                    EntityManagerInterface $entityManager,
                                    EtatRepository $etatRepository,
                                    Request $request): Response
{
    $participant = $participantRepository->find($id);
    $form = $this->createForm(SortieType::class, $participant);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid())
    {
        $button = $form->getClickedButton()->getName() ;
        switch ($button)
        {
            case "modifier" :
                $etat = $etatRepository->findOneBy(['libelle'=>'Ouverte']);
                $participant->setUnEtat($etat);
                $this->addFlash('success','Utilisateur modifié !');
                break;
            case "desactiver" :
                $etat = $etatRepository->findOneBy(['libelle'=>'Créée']);
                $participant->setUnEtat($etat);
                $this->addFlash('success','Utilisateur desactivé !');
                break;
            case "supprimer" :
                $this->addFlash('success','utilisateur supprimé !');
                $entityManager->remove($participant);
                $entityManager->flush();
                return $this->redirectToRoute('main_acceuil');

        }
        $entityManager->persist($participant);
        $entityManager->flush();

        return new response("Profil modifié");

    }*/

    /*eturn $this->render('sortie/modificationProfil.html.twig',[
        'participantForm' => $form->createView(),
        'participantModif'=> $participant
    ]);*/
}
