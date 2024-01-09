<?php

namespace App\Remote;

use App\Entity\Profil;

interface ProfilInterface
{
    /**
     * @return Profil[] Returns an array of Utilisateur objects
     */
    public function getAll() : array;

    public function saveProfil(Profil $Profil) : void;
    public function removeProfil(Profil $Profil) : void;
}
