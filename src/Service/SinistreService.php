<?php

namespace App\Service;

use App\Entity\Sinistre;
use App\Entity\Utilisateur;
use App\Remote\SinistreInterface;
use App\Repository\SinistreRepository;
use App\Repository\UtilisateurRepository;

class SinistreService implements SinistreInterface
{
    private SinistreRepository $sinistreRepository;

    public function __construct(SinistreRepository $sinistreRepository)
    {
        $this->sinistreRepository = $sinistreRepository;
    }


    /**
     * @return array|Sinistre
     */
    public function getAll(): array
    {

       return $this->sinistreRepository->findAll();
    }

    public function modifierSinistre(Sinistre $sinistre): void
    {
        $this->sinistreRepository->save($sinistre, true);
    }


    public function saveSinistre(Sinistre $Sinistre): void
    {
        $this->sinistreRepository->save($Sinistre, true);
    }

    public function removeSinistre(Sinistre $Sinistre): void
    {
        $this->sinistreRepository->remove($Sinistre, true);
    }

    public function getAllForAbonne(Utilisateur $utilisateur): array
    {
        return $this->sinistreRepository->findAllForAbonne($utilisateur);
    }

    public function countSinistre()
    {
        // TODO: Implement countSinistre() method.
        return $this->sinistreRepository->countSinistrePublies();
    }
}
