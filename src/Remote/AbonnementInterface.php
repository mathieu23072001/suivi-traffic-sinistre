<?php

namespace App\Remote;

use App\Entity\Utilisateur;

interface AbonnementInterface
{
    /**
     * @return Utilisateur[] Returns an array of Utilisateur objects
     */
    public function getAll() : array;

    public function modifierAbonnement(Utilisateur $utilisateur) : void;
}