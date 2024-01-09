<?php

namespace App\Service;

use App\Entity\InformationUtile;
use App\Entity\Sinistre;
use App\Entity\Utilisateur;
use App\Remote\InfoUtileInterface;
use App\Remote\SinistreInterface;
use App\Repository\InformationUtileRepository;
use App\Repository\SinistreRepository;
use App\Repository\UtilisateurRepository;

class InfoUtileService implements InfoUtileInterface
{
    private InformationUtileRepository $infoUtileRepository;

    public function __construct(InformationUtileRepository $informationUtileRepository)
    {
        $this->infoUtileRepository = $informationUtileRepository;
    }
    public function getAllForAbonne(Utilisateur $utilisateur): array
    {
        return $this->infoUtileRepository->findAllForAbonne($utilisateur);
    }

    public function saveDeclaration(InformationUtile $sinistre)
    {
        return $this->infoUtileRepository->save($sinistre);
    }
}
