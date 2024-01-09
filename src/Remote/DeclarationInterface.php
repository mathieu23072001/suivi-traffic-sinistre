<?php

namespace App\Remote;

use App\Entity\Declaration;
use App\Entity\Sinistre;
use App\Entity\Utilisateur;

interface DeclarationInterface
{
    /**
     * @return DECLARATION[] Returns an array of Sinistre objects
     */
    public function getAll() : array;

    public function findAllToPublish();

    public function modifierDeclaration(Declaration $declaration): void;

    public function getAllForAbonne(Utilisateur $user);


}

