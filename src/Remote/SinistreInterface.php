<?php

namespace App\Remote;

use App\Entity\Sinistre;
use App\Entity\Utilisateur;

interface SinistreInterface
{
    /**
     * @return Sinistre[] Returns an array of Sinistre objects
     */
    public function getAll() : array;

    public function modifierSinistre(Sinistre $sinistre):void;

    public function saveSinistre(Sinistre $Sinistre) : void;

    public function removeSinistre(Sinistre $Sinistre) : void;

    public function getAllForAbonne(Utilisateur $user): array;

    public function countSinistre();
}

