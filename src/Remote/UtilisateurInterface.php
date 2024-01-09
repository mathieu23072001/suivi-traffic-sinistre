<?php

namespace App\Remote;

use App\Entity\Sinistre;
use App\Entity\Utilisateur;

interface UtilisateurInterface
{
    public function isAbonne(Utilisateur $utilisateur): bool;

    public function countAbonnes();

    public function isAdmin(Utilisateur $utilisateur): bool;
}

