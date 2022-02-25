<?php

namespace App\Donnee;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Participant;
use phpDocumentor\Reflection\Types\Boolean;

class DonneeSorties
{
    /**
     * @var string
     */
    public string $mot = '';

    /**
     * @var Campus
     */
    public Campus $campus ;

    /**
     * @var \DateTime
     */
    public \DateTime $dateMin;

    /**
     * @var \DateTime
     */
    public \DateTime $dateMax;

    /**
     * @var Participant
     */
    public Participant $organisateur;

    /**
     * @var Participant[]
     */
    public array $participant = [];

    /**
     * @var Etat[]
     */
    public array $etat = [];

    /**
     * @var bool
     */
    public Boolean $organisateurTrue;

    /**
     * @var bool
     */
    public Boolean $organisateurFalse;

    /**
     * @var bool
     */
    public Boolean $inscritTrue;

    /**
     * @var bool
     */
    public Boolean $inscritFalse;

    /**
     * @var bool
     */
    public Boolean $passee;
}