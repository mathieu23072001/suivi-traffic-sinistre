<?php

namespace App\Controller;

use App\Entity\Profil;
use App\Remote\MercureInterface;
use App\Remote\SinistreInterface;
use App\Remote\UtilisateurInterface;
use App\Repository\InformationUtileRepository;
use App\Repository\ProfilRepository;
use App\Service\UtilisateurService;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use App\Repository\UtilisateurRepository;
use App\Entity\PositionGeographique;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use App\Repository\PositionGeographiqueRepository;
use App\Utils\Singleton;
use Doctrine\ORM\EntityManagerInterface;

class AcceuilController extends AbstractController
{

    private ProfilRepository $profilRepository;
    private MercureInterface $mercureService;
    private SinistreInterface $sinistreService;
    private InformationUtileRepository $infoService;
    private UtilisateurInterface $utilisateurService;
    private UtilisateurRepository $utilisateurRepository;
    private PositionGeographiqueRepository $positionGeographiqueRepository;
    private $entityManager;
    private $_compte;
    
    public function __construct(SinistreInterface $sinistreService, InformationUtileRepository $infoService, UtilisateurInterface $utilisateurService, Security $security, ProfilRepository $profilRepository, MercureInterface $mercureService, UtilisateurRepository $utilisateurRepository, PositionGeographiqueRepository $positionGeographiqueRepository, EntityManagerInterface $entityManager)
        {
            $this->security = $security;
            $this->profilRepository = $profilRepository;
            $this->mercureService = $mercureService;
            $this->sinistreService = $sinistreService;
            $this->infoService = $infoService;
            $this->utilisateurService = $utilisateurService;
            $this->utilisateurRepository = $utilisateurRepository;
            $this->positionGeographiqueRepository = $positionGeographiqueRepository;
            $this->entityManager = $entityManager;
            
        }


    #[Route('/', name: 'app_acceuil')]
    public function index(): Response
    {
        if ($this->security->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('app_acceuil_back');
        }
        $this->ckeckProfil();
        $position = [];
        $position['latitude'] = 6.175813;
        $position['longitude'] = 1.214423;
        $position['zoom'] = 17;
        $position['lieu'] = "Université de Lomé";

        return $this->render('acceuil/index.html.twig', [
            'position' =>$position,
        ]);
    }

    #[Route('/back', name: 'app_acceuil_back')]
    public function indexBack(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $position = [];
        $position['latitude'] = 6.175813;
        $position['longitude'] = 1.214423;
        $position['zoom'] = 17;
        $position['lieu'] = "Université de Lomé";
        $declarations = $this->mercureService->getDeclarationToPublish();
        //dd($declarations);
        if($this->profilRepository->isAdmin($this->getUser())) {
            return $this->render('back/usager/index.html.twig', [
                'position' => $position,
                'declarations' => $declarations,
            ]);
        }
        return $this->render('back/index.html.twig', [
            'position' =>$position,
            'declarations' =>$declarations,
        ]);
    }

    #[Route('/back/dashboard', name: 'app_acceuil_dashboard')]
    public function dashboard(): Response
    {
//        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->security->isGranted('IS_AUTHENTICATED_REMEMBERED');
        $position = [];
        $position['latitude'] = 6.175813;
        $position['longitude'] = 1.214423;
        $position['zoom'] = 17;
        $position['lieu'] = "Université de Lomé";
        $nbAbonnes = $this->utilisateurService->countAbonnes();
        $nbSinistres = $this->sinistreService->countSinistre();
        $nbInfos = $this->infoService->countInfo();
        $declarations = $this->mercureService->getDeclarationToPublish();
        return $this->render('acceuil/tableauBord.html.twig', [
            'nbAbonnes' =>$nbAbonnes,
            'nbDeclations' =>($nbSinistres + $nbInfos),
            'nbSinistres' =>$nbSinistres,
            'nbInfos' =>$nbInfos,
            'position' =>$position,
            'declarations' =>$declarations,
        ]);
    }

    private function ckeckProfil()
    {
        $profils = $this->profilRepository->findAll();

        if(empty($profils)){
            $p1 = new Profil();
            $p1->setCode("ROLE_USAGER");
            $p1->setLibelle("Usager");

            $p2 = new Profil();
            $p2->setCode("ROLE_AUTORITE");
            $p2->setLibelle("Autorite");

            $p3 = new Profil();
            $p3->setCode("ROLE_ADMIN");
            $p3->setLibelle("Administrateur");

            $this->profilRepository->save($p1);
            $this->profilRepository->save($p2);
            $this->profilRepository->save($p3);
        }
    }

    #[Route('/test', name: 'app_acceuil_test')]
    public function test(HttpClientInterface $client): Response {
        $way_id = 256771228;
        $query = "[out:json];way($way_id);(._;>;);out;";

// effectuer la requête
        $url = "http://overpass-api.de/api/interpreter?data=" . urlencode($query);
        $response = file_get_contents($url);

// décoder la réponse
        $data = json_decode($response, true);

// extraire les points géographiques de la voie
        $points = array();
        foreach ($data["elements"] as $element) {
            if ($element["type"] == "node") {
                $points[] = array(
                    "lat" => $element["lat"],
                    "lon" => $element["lon"]
                );
            }
        }

// échantillonner les points géographiques pour obtenir au moins 50
        $sampled_points = array();
        if (count($points) > 50) {
            $interval = ceil(count($points) / 50);
            for ($i = 0; $i < count($points); $i += $interval) {
                $sampled_points[] = $points[$i];
            }
        } else {
            $sampled_points = $points;
        }
        //dd($sampled_points);





// construire la requête Overpass API
        $query = "[out:json];way($way_id);(._;>;);out;";

// effectuer la requête
        $url = "http://overpass-api.de/api/interpreter?data=" . urlencode($query);
        dd($url);
        $response = file_get_contents($url);

// décoder la réponse
        $data = json_decode($response, true);

// extraire les points géographiques
        $points = array();
        foreach ($data["elements"] as $element) {
            if ($element["type"] == "node") {
                $points[] = array(
                    "lat" => $element["lat"],
                    "lon" => $element["lon"]
                );
            }
        }
        dd($points);




        $street = "Rue de Rivoli";
        $city = "Paris";
        $country = "France";

        $url = "https://nominatim.openstreetmap.org/search";
        $response = $client->request(
            'GET',
            $url,
            [
                'query' => [
                    'city' => 'Lomé',
                    'country' => 'Togo',
                    'street' => 'Avenue Akéi',
                    'addressdetails' => '1',
                    'format' => 'json',
                    'limit' => '500',
                ]
            ]
        );
        $data = null;
        $statusCode = $response->getStatusCode();
        if($statusCode== 200){
            $content = $response->getContent();
            $data = json_decode($content, true);
        }
//        dd($statusCode);
//        $response = file_get_contents($url);

        dd($data);
        $latitude = $data[0]["lat"];
        $longitude = $data[0]["lon"];

        echo "Latitude: $latitude, Longitude: $longitude";
    }

    #[Route('/testajax', name: 'app_accueil_ajax')]
    public function receptionAjax(Request $request)
    {
        $t = "rrr";
        //$request->
        //$lat = $request->query->get('latitude');
        //$a = (isset($_POST["latitude"])) ? $_POST["latitude"] : NULL;
        //$_compte = $lat;
        echo $t;
        
        $response = new Response(json_encode(array('status' => 'success')));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
        return $this->render('acceuil/index.html.twig',['latitude'=>$lat]);
    }
/*
    public function getLastPointId()
    {
        $query = $this->entityManager->createQuery('SELECT id FROM App\Entity\PositionGeographique 
        ORDER BY id DESC')->setMaxResults(1);

        $result = $query->getResult();

        return $result[0]['id'];
    }
*/
#[Route('/recup', name: 'recup')]
public function recup(UtilisateurRepository $repo, Request $request): Response
{
    $lat = $request->request->get('latitude');
    $lon = $request->request->get('longitude');
    $datePosition = $request->request->get('datePosition');
    $listePoints[]=[$lat, $lon];

    return $this->redirectToRoute('app_api', [
            'pointsRecup' => $listePoints,
        ]);
    
}


    #[Route('/trace', name: 'app_trace')]
    public function trace(UtilisateurRepository $repo, Request $request): Response
    {
        $lat = $request->request->get('latitude');
        $lon = $request->request->get('longitude');
        $datePosition = $request->request->get('datePosition');
        //dd($lat, $lon, $request);
        //dump($request);
        //Recupération de lat et lng
        //$lat = (isset($_POST["latitude"])) ? $_POST["latitude"] : NULL;
        //$lon = (isset($_POST["longitude"])) ? $_POST["longitude"] : NULL;
        //$lat = 6.1789965;
        //$lon = 1.220084;

        //Intialisation de la liste et du tableau de points
        //$listePoints = [];
        //$points = [];

        //Récupération des usagers de la BD
        $usagers= $repo->findAll();
        //On crée un usager à affecter aux points géographiques futurs
        $user = $usagers[0];
        //Récupération de l'ID du dernier point géographique
        $i = 1;
        //On initialise un intervalle de temps dans lequel on choisira une date pour le futur point géographique 
        //$start = strtotime("10 february 2022");
        //$end = strtotime("10 february 2022");

//        while() {
        //Instanciation d'un nouveau point géographique
        $positionsGeo = new PositionGeographique();
        //$n = rand(0, count($usagers)-1);    
        //$lat=  Utilitaire::random_float($latMin, $latMax);
        //$lon=  Utilitaire::random_float($lonMin, $lonMax);
        //$lat = $points[$i][0];
        //$lon = $points[$i][1];

        //On génère une date pour le point géographique 
        //$string = rand($start, $end);
        //$dateS =  date("Y-m-d H:i:s", $string);
        $dateTimestamp = date("Y-m-d H:i:s", $datePosition);
        $date = new DateTimeImmutable($dateTimestamp);
     
        //$positionsGeo->setId($i);
        $positionsGeo->setDatePosition($date);
        $positionsGeo->setLatitude($lat);
        $positionsGeo->setLongitude($lon);
        $positionsGeo->setUtilisateur($user);

        $listePoints[] = $positionsGeo;

        $this->positionGeographiqueRepository->save($positionsGeo);
            Singleton::getInstance()->addPoint($positionsGeo);
            
//    }
        //dump(Singleton::getInstance());

            return $this->redirectToRoute('push');
        
    }

}

