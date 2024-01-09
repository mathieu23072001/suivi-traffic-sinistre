<?php

namespace App\Service;

use App\Entity\Profil;
use App\Repository\ProfilRepository;

class ProfilService implements \App\Remote\ProfilInterface
{


  private ProfilRepository $profilRepository;

  public function __construct(ProfilRepository $profilRepository)
  {
      $this->profilRepository = $profilRepository;
  }
    /**
     * @return array|Profil
     */
    public function getAll(): array
    {
       return $this->profilRepository->findAll();
    }

    public function saveProfil(Profil $profil): void
    {
        $this->profilRepository->save($profil, true);
    }

    public function removeProfil(Profil $profil): void
    {
        $this->profilRepository->remove($profil, true);
    }
}
