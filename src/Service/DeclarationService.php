<?php

namespace App\Service;

use App\Entity\Declaration;
use App\Entity\Sinistre;
use App\Entity\Utilisateur;
use App\Remote\DeclarationInterface;
use App\Remote\SinistreInterface;
use App\Repository\DeclarationRepository;
use App\Repository\SinistreRepository;
use App\Repository\UtilisateurRepository;

class DeclarationService implements DeclarationInterface
{
    private DeclarationRepository $declarationRepository;

    public function __construct(DeclarationRepository $declarationRepository)
    {
        $this->declarationRepository = $declarationRepository;
    }


    /**
     * @return array|Declaration
     */
    public function getAll(): array
    {

       return $this->declarationRepository->findAll();
    }

    public function findAllToPublish()
    {
        return $this->declarationRepository->findAllToPublish();
}
    public function modifierDeclaration(Declaration $declaration): void
    {
        $this->declarationRepository->save($declaration, true);
    }

    public function getAllForAbonne(Utilisateur $user)
    {
        return $this->declarationRepository->getAllForAbonne($user);
    }
}
