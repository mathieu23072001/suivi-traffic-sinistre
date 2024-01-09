<?php

namespace App\Service;

use App\Entity\Utilisateur;
use App\Remote\UtilisateurInterface;
use App\Repository\ProfilRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Component\Security\Core\Security;

class UtilisateurService implements UtilisateurInterface
{

    private ProfilRepository $profilRepository;
    private UtilisateurRepository $utilisateurRepository;
    private Security $security;

    public function __construct(Security $security, ProfilRepository $profilRepository, UtilisateurRepository $utilisateurRepository)
    {
        $this->profilRepository = $profilRepository;
        $this->utilisateurRepository = $utilisateurRepository;
        $this->security = $security;
    }

    public function isAbonne(Utilisateur $utilisateur): bool{
        $profil = $this->profilRepository->findOneBy([
            'code'=>'ROLE_ABONNE'
        ]);
        if ($utilisateur->getProfils()->contains($profil)) {
            return true;
        }
        return false;
    }

    public function countAbonnes()
    {
        // TODO: Implement countAbonnes() method.
        return $this->utilisateurRepository->coutAbonnes();
    }

    public function isAdmin(Utilisateur $utilisateur): bool
    {
        return  $this->security->isGranted('ROLE_ADMIN');
    }
}