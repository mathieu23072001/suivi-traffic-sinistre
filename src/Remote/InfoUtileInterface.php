<?php

namespace App\Remote;

use App\Entity\InformationUtile;
use App\Entity\Sinistre;
use App\Entity\Utilisateur;

interface InfoUtileInterface
{
//    /**
//     * @return InformationUtile[] Returns an array of Sinistre objects
//     */
//    public function getAll() : array;

    public function getAllForAbonne(Utilisateur $user): array;

    public function saveDeclaration(InformationUtile $sinistre);


}

