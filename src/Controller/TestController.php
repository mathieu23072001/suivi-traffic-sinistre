<?php

namespace App\Controller;

use App\Remote\CalculMoyenneInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Utilisateur;
use App\Entity\PositionGeographique;

use App\Entity\Voie;
use App\Repository\PositionGeographiqueRepository;
use App\Repository\VoieRepository;
use App\Service\VoieService;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use App\Utils\Utilitaire;
use App\Repository\UtilisateurRepository;
use App\Utils\Singleton;
use Symfony\Component\HttpFoundation\Request;



class TestController extends AbstractController
{

    private PositionGeographiqueRepository $positionGeographiqueRepository;
    private VoieRepository $voieRepository;
    private VoieService $voieService;
    private UtilisateurRepository $utilisateurRepository;
   

    public function __construct(UtilisateurRepository $utilisateurRepository, VoieService $voieService, PositionGeographiqueRepository $positionGeographiqueRepository, VoieRepository $voieRepository)
    {

        $this->positionGeographiqueRepository = $positionGeographiqueRepository;
        $this->voieRepository = $voieRepository;
        $this->voieService = $voieService;
        $this->utilisateurRepository = $utilisateurRepository;
    }

    #[Route('/tracetest', name: 'app_trace_test')]
    public function trace(UtilisateurRepository $repo): Response
    {
        $listePoints = [];
        $points = [];
        $points[0] = [6.175813, 1.214423];
        $points[1] = [6.175995, 1.214555];
        $points[2] = [6.175491, 1.214623];
        $points[3] = [6.175473, 1.214871];
        $points[4] = [6.175062, 1.214926];
        $points[5] = [6.174736, 1.215024];
        $points[6] = [6.174701, 1.215256];
        $points[7] = [6.174600, 1.215353];
        $points[8] = [6.174058, 1.215686];
        $points[9] = [6.173825, 1.215588];
        $points[10] = [6.173613, 1.215507];
        $points[11] = [6.173520, 1.215864];
        $points[12] = [6.173173, 1.216639];
        $points[13] = [6.173016, 1.216946];
        $points[14] = [6.172707, 1.217508];
        $points[15] = [6.172398, 1.218121];
        $points[16] = [6.172085, 1.218726];
        $points[17] = [6.171873, 1.219164];
        $points[18] = [6.171658, 1.219679];
        $points[19] = [6.171674, 1.219862];
        $points[20] = [6.178975, 1.215998];
        $points[21] = [6.180201, 1.215376];
        $points[22] = [6.181055, 1.214947];
        $points[23] = [6.180231, 1.215365];
        $points[24] = [6.184733, 1.213241];
        $points[25] = [6.188658, 1.211331];
        $points[26] = [6.166684, 1.201875];
        $points[27] = [6.167580, 1.201403];
        $points[28] = [6.168519, 1.200717];
        $points[29] = [6.169458, 1.199429];
        $points[30] = [6.169799, 1.198185];
        $points[31] = [6.170226, 1.194580];
        $points[32] = [6.170695, 1.192134];
        $points[33] = [6.172828, 1.187499];
        $points[34] = [6.174364, 1.183980];


        $usagers= $repo->findAll();
        //dd($usagers);
        $latMin = 6.000000;
        $latMax = 6.999999;
        $lonMin = 1.000000;
        $lonMax = 1.999999;
        $start = strtotime("10 february 2022");
        $end = strtotime("10 february 2022");

        for ($i = 0; $i < count($points); $i++){
        $positionsGeo = new PositionGeographique();
        $n = rand(0, count($usagers)-1);
        //$user = $usagers[0];
        if($i < 26)
        {
            $user = $usagers[0];
        }
        else {
            $user = $usagers[1];
        }
        
        
        //$lat=  Utilitaire::random_float($latMin, $latMax);
        //$lon=  Utilitaire::random_float($lonMin, $lonMax);
        $lat = $points[$i][0];
        $lon = $points[$i][1];
        $string = rand($start, $end);
        $dateS =  date("Y-m-d H:i:s", $string);
        $date = new DateTimeImmutable($dateS);
     
        $positionsGeo->setId($i);
        $positionsGeo->setDatePosition($date);
        $positionsGeo->setLatitude($lat);
        $positionsGeo->setLongitude($lon);
        $positionsGeo->setUtilisateur( $user);

        $listePoints[$i] = $positionsGeo;

        $this->positionGeographiqueRepository->save($positionsGeo);
            Singleton::getInstance()->addPoint($positionsGeo);
        }

        //dump(Singleton::getInstance());


        return $this->redirectToRoute('push');
        
    }

}
