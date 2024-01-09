<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EtatTraficController extends AbstractController
{
    #[Route('/trafic', name: 'app_etat_trafic_rayon')]

    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $position = [];
        $position['latitude'] = 6.175813;
        $position['longitude'] = 1.214423;
        $position['zoom'] = 17;
        $position['lieu'] = "Université de Lomé";

        return $this->render('etat_trafic/index.html.twig', [
            'position' =>$position,
        ]);

    }
}
