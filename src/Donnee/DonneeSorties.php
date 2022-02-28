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
    public $mot = '';

    /**
     * @var Campus
     */
    public Campus $campus ;

    /**
     * @var \DateTime
     */
    public $dateMin;


    /**
     * @var \DateTime
     */
    public $dateMax;

    /**
     * @var Etat[]
     */
    public array $etat = [];

    /**
     * @var bool
     */
    public $organisateurTrue;

    /**
     * @var bool
     */
    public $organisateurFalse;

    /**
     * @var bool
     */
    public $inscritTrue;

    /**
     * @var bool
     */
    public $inscritFalse;

    /**
     * @var bool
     */
    public $passee;

    /**
     * @var bool
     */
    public $ouverte;

    /**
     * @var bool
     */
    public $creee;

}