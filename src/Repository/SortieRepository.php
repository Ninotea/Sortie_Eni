<?php

namespace App\Repository;

use App\Donnee\DonneeDeRecherche;
use App\Donnee\DonneeSorties;
use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function rechercheSortie(DonneeSorties $donnees,Participant $currentUser)
    {
        // possibilité d'ajouter un paginator ICI
        //dd($this->getRequete($donnees,$currentUser)->getQuery()->getResult());
        return $this->getRequete($donnees,$currentUser)->getQuery()->getResult();
    }

    public function getRequete(DonneeSorties $donnees,Participant $currentUser)
    {
        $query = $this
            ->createQueryBuilder('sorty') //sorty pour sortie recherché (n'est pas une entité sortie)
            ->select('sorty','etat','li','campus','orga','partici')
            ->join('sorty.unEtat','etat')
            ->join('sorty.lieu','li')
            ->join('sorty.leCampus','campus')
            ->join('sorty.organisateur','orga')
            ->leftJoin('sorty.participants','partici');
            if(!empty($donnees->mot))
            {
                $query = $query
                    ->andWhere('sorty.nom LIKE :motRecherche')
                    ->orWhere('sorty.infosSortie LIKE :motRecherche')
                    ->setParameter('motRecherche',"%{$donnees->mot}%");
            }
            if(!empty($donnees->campus))
            {
                $query = $query
                    ->andWhere('campus.id = :campusId') // les nom utilsiée doivent correpsodnre au alias
                    ->setParameter('campusId', $donnees->campus->getId());
            }
            if($donnees->passee === false)
            {
                $query = $query
                    ->andWhere('etat.libelle NOT LIKE :etatPassee')
                    ->setParameter('etatPassee', 'passée' );
            }
            if($donnees->creee === false)
            {
                $query = $query
                    ->andWhere('etat.libelle NOT LIKE :etatCreee')
                    ->setParameter('etatCreee', 'créée' );
            }
            if($donnees->ouverte === false)
            {
                $query = $query
                    ->andWhere('etat.libelle NOT LIKE :etatOuverte')
                    ->setParameter('etatOuverte', 'ouverte' );
            }
            if(!empty($donnees->dateMin) && !empty($donnees->dateMax))
            {
                $query = $query
                    ->andWhere('sorty.dateHeureDebut BETWEEN :datemin AND :datemax')
                    ->setParameter('datemin',$donnees->dateMin)
                    ->setParameter('datemax',$donnees->dateMax);
            }

            /*
             * FILTRE ORGANISATEUR
             */
            // où je ne suis pas organisateur
            if($donnees->organisateurTrue === false && $donnees->organisateurFalse === true){
                $query = $query
                    ->andWhere('orga.id != :currentUserId')
                    ->setParameter('currentUserId', $currentUser->getId() );
            //où je suis organisateur
            }elseif($donnees->organisateurFalse === false && $donnees->organisateurTrue === true){
                $query = $query
                    ->andWhere('orga.id = :currentUserId')
                    ->setParameter('currentUserId', $currentUser->getId() );
            // où il n'y a pas d'organisteur (ni moi ni les autres)
            }elseif($donnees->organisateurFalse === false && $donnees->organisateurTrue === false){
                // Dans le else La requette renverra à chaque fois aucune donnée
                $query = $query
                    ->andWhere('orga.id = :currentUserId')
                    ->andWhere('orga.id != :currentUserId')
                    ->setParameter('currentUserId', $currentUser->getId() );
            }// si les deux sont cochés on ne filtre pas

            /*
             * FILTRE Participant
             */
            //où je ne suis pas inscrit
            if($donnees->inscritTrue === false && $donnees->inscritFalse === true){
                $query = $query
                    ->andWhere(' partici.id NOT IN (:currentUser)')
                    ->orWhere('partici IS NULL') // permet de faire fonctionner la requette, si la table de relation est vide  SQL ne renvoie rien
                    ->setParameter('currentUser', $currentUser->getId() );
            //où je suis inscrit
            }elseif($donnees->inscritFalse === false && $donnees->inscritTrue === true){
                $query = $query
                    ->andWhere('partici IN (:currentUser)')
                    ->setParameter('currentUser', $currentUser );
            //où je suis ni inscrit ni pas inscrit (donc aucune)
            }elseif($donnees->inscritFalse === false && $donnees->inscritTrue === false){
                // Dans le else La requette renverra à chaque fois aucune donnée
                $query = $query
                    ->andWhere('partici NOT IN (:currentUser)')
                    ->andWhere('partici IN (:currentUser)')
                    ->setParameter('currentUser', $currentUser );
            }// où Je suis inscrit ou pas inscrit (tout) donc on filtre pas

        $query->addOrderBy('sorty.dateHeureDebut','ASC');

        return $query;
    }

    public function updateEtatSortie(EtatRepository $etatRepository){
        $dateDuJour = new \DateTime();
        $JourDuJour = (int) date_format($dateDuJour,'d');
        $MoisDuJour = (int) date_format($dateDuJour,'m');
        $AnneeDuJour = (int) date_format($dateDuJour,'Y');
        $dateMoinsUnMois = new \DateTime();
        $dateMoinsUnMois->setDate($AnneeDuJour,$MoisDuJour-1,$JourDuJour);
        $etatpassee = $etatRepository->findOneBy(['libelle'=>'Passée']);
        $etatCloturee = $etatRepository->findOneBy(['libelle'=>'Clôturée']);

        $query = $this
        ->createQueryBuilder('etatPassee')
            ->update('App:Sortie','sorty')
            ->set('sorty.unEtat',$etatpassee->getId())
            ->where('sorty.dateHeureDebut <= :datedujour')
            ->setParameter('datedujour',$dateDuJour)
            ->getQuery();

        $query->execute();

        $query = $this
            ->createQueryBuilder('etatCloture')
            ->update('App:Sortie','sorty')
            ->set('sorty.unEtat',$etatCloturee->getId())
            ->where('sorty.dateHeureDebut <= :dateMoinsUnMois')
            ->setParameter('dateMoinsUnMois',$dateMoinsUnMois)
            ->getQuery();

        $query->execute();

    }
}
