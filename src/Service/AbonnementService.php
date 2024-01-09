<?php

namespace App\Service;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;

class AbonnementService implements \App\Remote\AbonnementInterface
{
    private UtilisateurRepository $utilisateurRepository;

    public function __construct(UtilisateurRepository $utilisateurRepository)
    {
        $this->utilisateurRepository = $utilisateurRepository;
    }

    /**
     * @return array|Utilisateur
     */
    public function getAll(): array
    {
       return $this->utilisateurRepository->findAll();
    }

    public function modifierAbonnement(Utilisateur $utilisateur): void
    {
        $this->utilisateurRepository->save($utilisateur, true);
    }
}