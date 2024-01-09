<?php

namespace App\Controller;

use App\Entity\PositionGeographique;
use App\Entity\Utilisateur;
use App\Remote\CalculMoyenneInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class MercureController extends AbstractController
{

    private HubInterface $hub;
    private Serializer $serializer;
    private CalculMoyenneInterface $calculMoyenneService;

    public function __construct(HubInterface $hub, CalculMoyenneInterface $calculMoyenneService)
    {
        $this->serializer = new Serializer(
            [new GetSetMethodNormalizer(), new ArrayDenormalizer()],
            [new JsonEncoder()]
        );
        $this->hub = $hub;
        $this->calculMoyenneService = $calculMoyenneService;
    }

    #[Route('/push', name: 'push', methods: ['POST', 'GET'])]
    public function publish(HubInterface $hub): Response
    {
        /**$position = [];
        $position['latitude'] = 6.1221;
        $position['longitude'] = 1.2111;
        $position['zoom'] = 25;
        $position['lieu'] = "zone actualisée";*/    


        $resultat = $this->calculMoyenneService->data();


        $update = new Update(
            'ping',
            json_encode(['position' => $resultat])
        );

        $hub->publish($update);

       return new Response('published!');

        return $this->redirectToRoute('app_acceuil');
    }
}
