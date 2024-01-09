<?php

namespace App\Controller;

use App\Entity\Voie;
use App\Repository\PositionGeographiqueRepository;
use App\Repository\VoieRepository;
use App\Service\VoieService;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use App\Utils\Utilitaire;
use App\Entity\Utilisateur;
use App\Entity\PositionGeographique;
use App\Repository\UtilisateurRepository;
use App\Utils\Singleton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
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

    #[Route('/api2', name: 'app_api2')]
    public function api2(UtilisateurRepository $repo): Response
    {
        $voies = $this->voieRepository->findAll();
        $n = 0;
        foreach ($voies as $voie) {
            $pts = $this->voieService->getPointGeographique($voie->getIdOSM());
            $datePostion = new \DateTime();

            foreach ($pts as $pt) {
                $this->createPointGeoGraphique($pt, $voie, $datePostion, $n);
                $interval = new DateInterval("PT10S");
                $datePostion = ($datePostion->add($interval));
            }
            $n++;
        }
        return $this->redirectToRoute('push');
    }

    #[Route('/api', name: 'app_api')]
    public function index(UtilisateurRepository $repo, Request $request): Response
    {
        //$points2 = $request->query->get('pointsRecup');
        //$listePoints = [];
        $points = [];
        //Boulevard Eyadema en partant du bar 3K jusqu'à l'entrée GTA
        $points[0] = [6.165293, 1.222719];
        $points[1] = [6.166157, 1.222343];
        $points[2] = [6.168682, 1.221211];
        $points[3] = [6.170265, 1.220499];
        $points[4] = [6.171636, 1.219826];
        $points[5] = [6.172952, 1.219153];
        $points[6] = [6.174000, 1.218609];
        $points[7] = [6.175081, 1.218026];
        $points[8] = [6.176424, 1.217331];
        $points[9] = [6.177467, 1.216748];
        $points[10] = [6.178999, 1.215968];
        $points[11] = [6.180103, 1.215397];
        $points[12] = [6.182188, 1.214399];
        $points[13] = [6.184166, 1.213440];
        $points[14] = [6.186881, 1.212145];
        $points[15] = [6.189679, 1.210928];
        $points[16] = [6.191931, 1.209852];
        //Rond point GTA
        $points[17] = [6.191601, 1.210041];
        $points[18] = [6.191951, 1.209838];
        $points[19] = [6.192389, 1.209636];
        $points[20] = [6.192836, 1.209574];
        $points[21] = [6.193178, 1.209565];
        $points[22] = [6.193519, 1.209574];
        $points[23] = [6.193835, 1.209530];
        $points[24] = [6.194133, 1.209521];
        $points[25] = [6.194317, 1.209371];
        //Supermarché Concorde vers Avénou
        $points[26] = [6.166684, 1.201875];
        $points[27] = [6.167580, 1.201403];
        $points[28] = [6.168519, 1.200717];
        $points[29] = [6.169458, 1.199429];
        $points[30] = [6.169799, 1.198185];
        $points[31] = [6.170226, 1.194580];
        $points[32] = [6.170695, 1.192134];
        $points[33] = [6.172828, 1.187499];
        $points[34] = [6.174364, 1.183980];
        //Du rond-point Colombe vers stade Kégué
        $points[35] = [6.149615, 1.230084];
        $points[36] = [6.150740, 1.230084];
        $points[37] = [6.151613, 1.229807];
        $points[38] = [6.152657, 1.229281];
        $points[39] = [6.153610, 1.228721];
        $points[40] = [6.155223, 1.227832];
        $points[41] = [6.156251, 1.227335];
        $points[42] = [6.157393, 1.226671];
        $points[43] = [6.157870, 1.226342];
        $points[44] = [6.160264, 1.225095];
        $points[45] = [6.161670, 1.224414];
        $points[46] = [6.162922, 1.223721];
        $points[47] = [6.165046, 1.222918];
        $points[48] = [6.165373, 1.223727];
        $points[49] = [6.165803, 1.225020];
        $points[50] = [6.166268, 1.226925];
        $points[51] = [6.166992, 1.229604];
        $points[52] = [6.167267, 1.230147];
        $points[53] = [6.167537, 1.230771];
        $points[54] = [6.167732, 1.231423];
        $points[55] = [6.167968, 1.232214];
        $points[56] = [6.168392, 1.232861];
        $points[57] = [6.168725, 1.234206];
        $points[58] = [6.168829, 1.234709];
        $points[59] = [6.170252, 1.234651];
        $points[60] = [6.170838, 1.234628];
        $points[61] = [6.171177, 1.234616];
        $points[62] = [6.172658, 1.234564];
        $points[63] = [6.175298, 1.234414];
        $points[64] = [6.178214, 1.234264];
        $points[65] = [6.179299, 1.234189];
        $points[66] = [6.182606, 1.235124];
        $points[67] = [6.185413, 1.236129];
        $points[68] = [6.188926, 1.236833];
        $points[69] = [6.191750, 1.237688];
        $points[70] = [6.194878, 1.238693];
        $points[71] = [6.198282, 1.239859];
        $points[72] = [6.202656, 1.241418];
        //Boulevard du Haho
        $points[73] = [6.201741, 1.248580];
        $points[74] = [6.195354, 1.245965];
        $points[75] = [6.189093, 1.243364];
        $points[76] = [6.182062, 1.240426];
        $points[77] = [6.181154, 1.240173];
        $points[78] = [6.179630, 1.240285];
        $points[79] = [6.172628, 1.240960];
        //Avénou vers Adidogomé
        $points[80] = [6.176893, 1.178493];
        $points[81] = [6.180189, 1.172747];
        $points[82] = [6.187680, 1.163643];
        $points[83] = [6.196534, 1.153566];
        $points[84] = [6.204794, 1.143798];
        $points[85] = [6.213648, 1.133412];
        //Supermarché Concorde vers GTA
        $points[86] = [6.166620, 1.202238];
        $points[87] = [6.168800, 1.202864];
        $points[88] = [6.170274, 1.203222];
        $points[89] = [6.172294, 1.203722];
        $points[90] = [6.174083, 1.204157];
        $points[91] = [6.175781, 1.204586];
        $points[92] = [6.177633, 1.205043];
        $points[93] = [6.179073, 1.205437];
        $points[94] = [6.180932, 1.205915];
        $points[95] = [6.182826, 1.206343];
        $points[96] = [6.184692, 1.206885];
        $points[97] = [6.187137, 1.207503];
        $points[98] = [6.189087, 1.207932];
        $points[99] = [6.191142, 1.208459];


        $usagers= $repo->findAll();
        //dd($usagers);
        //$latMin = 6.000000;
        //$latMax = 6.999999;
        //$lonMin = 1.000000;
        //$lonMax = 1.999999;
        $start = strtotime("10 february 2022");
        $end = strtotime("10 february 2022");

        for ($i = 0; $i < count($points); $i++){
        $positionsGeo = new PositionGeographique();
        //$n = rand(0, count($usagers)-1);
        //$user = $usagers[0];
        if($i < 17)
        {
            $user = $usagers[0];
        }
        else if($i < 26){
            $user = $usagers[1];
        }
        else if($i < 35){
            $user = $usagers[2];
        }
        else if($i < 73){
            $user = $usagers[3];
        }
        else if($i < 80){
            $user = $usagers[4];
        }
        else if($i < 86){
            $user = $usagers[5];
        }
        else {
            $user = $usagers[6];
        }
        //$lat=  Utilitaire::random_float($latMin, $latMax);
        //$lon=  Utilitaire::random_float($lonMin, $lonMax);
        $lat = $points[$i][0];
        $lon = $points[$i][1];
        $string = rand($start, $end);
        $dateS =  date("Y-m-d H:i:s", $string);
        $date = new DateTimeImmutable($dateS);
     
        //$positionsGeo->setId($i);
        $positionsGeo->setDatePosition($date);
        $positionsGeo->setLatitude($lat);
        $positionsGeo->setLongitude($lon);
        $positionsGeo->setUtilisateur( $user);

        //$listePoints[$i] = $positionsGeo;

        $this->positionGeographiqueRepository->save($positionsGeo);
            Singleton::getInstance()->addPoint($positionsGeo);
        }

        //dump(Singleton::getInstance());


        return $this->redirectToRoute('push');
        
    }

    private function createPointGeoGraphique(mixed $pt, Voie $voie, $datePostion, $n)
    {
        $usagers = $this->utilisateurRepository->findAll();
//        $n = rand(0, count($usagers)-1);
        $user = $usagers[$n];

        $point = new PositionGeographique();
        $point->setVoie($voie);
        $point->setActif(true);
        $point->setUtilisateur($user);
        $point->setLongitude($pt['lon']);
        $point->setLatitude($pt['lat']);
        $point->setDatePosition($datePostion);
        $this->positionGeographiqueRepository->save($point, true);
    }
}
