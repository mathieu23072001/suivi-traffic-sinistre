<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Remote\AbonnementInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AbonnementController extends AbstractController
{

    private AbonnementInterface $abonnementService;

    public function __construct(AbonnementInterface $abonnementService)
    {
        $this->abonnementService = $abonnementService;
    }

    #[Route('/abonnement', name: 'app_abonnement')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $abonnement = $this->abonnementService->getAll();
        return $this->render('abonnement/index.html.twig', [
            'abonnements' => $abonnement,
        ]);
    }

    #[Route('/abonnement/active/{id}', name: 'app_abonnement_active')]
    public function activerAbonnement(Request $request, Utilisateur $utilisateur): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $utilisateur->setAbonnementActif(true);
        $this->abonnementService->modifierAbonnement($utilisateur);
        return  $this->redirectToRoute('app_abonnement');
    }

    #[Route('/abonnement/desactive/{id}', name: 'app_abonnement_desactive')]
    public function desactiverAbonnement(Request $request, Utilisateur $utilisateur): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $utilisateur->setAbonnementActif(false);
        $this->abonnementService->modifierAbonnement($utilisateur);
        return  $this->redirectToRoute('app_abonnement');
    }
}
