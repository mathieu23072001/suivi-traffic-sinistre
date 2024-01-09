<?php

namespace App\Service;

use App\Entity\Utilisateur;
use App\Repository\PositionGeographiqueRepository;
use App\Utils\Singleton;
use Doctrine\ORM\Query\Expr\Math;
use App\Entity\PositionGeographique;
use App\Remote\CalculMoyenneInterface;
use App\Repository\UtilisateurRepository;

class CalculMoyenneInterfaceService implements CalculMoyenneInterface
{

    private UtilisateurRepository $utilisateurRepository;
    const DISTANCE_MINIMAL = 5;
    const DUREE_MINIMAL = 20;
    private PositionGeographiqueRepository $positionGeographiqueRepository;

    public function __construct(UtilisateurRepository $utilisateurRepository, PositionGeographiqueRepository $positionGeographiqueRepository)
    {
        $this->utilisateurRepository = $utilisateurRepository;
        $this->positionGeographiqueRepository = $positionGeographiqueRepository;
    }

    public function listUsagers(array $tabTrie): ?array
    {
        $unique_Idusagers = array();
        $unique_Usagers = array();
        foreach ($tabTrie as $item) {
            if (!in_array($item->getUtilisateur()->getId(), $unique_Idusagers)) {
                $unique_Idusagers[] = $item->getUtilisateur()->getId();
                $unique_Usagers[] = $item->getUtilisateur();
            }
        }
//        foreach ($unique_Idusagers as $idUsager) {
//            $unique_Usagers[] = $this->utilisateurRepository->find($idUsager);
//        }
        return $unique_Usagers;
    }

    public function initialisationVoies(array $tabUsagers): ?array
    {
        foreach ($tabUsagers as &$tabUsager) {
            $tabUsager[] = null;
        }
        return $tabUsagers;
    }

    public function affectationVoie(array &$tab1, array &$tab2): void
    {
        $this->initialiseNomVoie($tab1);
        if($this->hasVoie($tab2)){
            return;
        }
        $pointFixe = $tab1[0];
        foreach ($tab2 as $item) {
            if (!is_null($item) && ($item instanceof PositionGeographique)) {
                if ($this->estVoisin($pointFixe, $item)) {
                    end($tab2);
                    $key = key($tab2);
                    $tab2[$key] = $pointFixe->getVoie->getId();
                    return;
                }
            }
        }
    }

    public function estVoisin(PositionGeographique $point1, PositionGeographique $point2): bool
    {
        if ($this->calculDistance($point1, $point2) <= self::DISTANCE_MINIMAL &&
            $this->duree($point1, $point2) <= self::DUREE_MINIMAL) {
            return true;
        }
        return false;
    }

    public function duree(PositionGeographique $point1, PositionGeographique $point2): int
    {
        $interval = $point1->getDatePosition()->diff($point2->getDatePosition());
        return $this->intervalToSeconds($interval);
    }

    /**
     * intervalToSeconds
     *
     * @param DateInterval $interval
     * @return int
     */
    private function intervalToSeconds(\DateInterval $interval)
    {
        return $interval->days * 86400 + $interval->h * 3600 + $interval->i * 60 + $interval->s;
    }

    private function initialiseNomVoie(array &$tab1): array
    {
        $voie = $tab1[0]->getUtilisateur()->getId();
        if(is_null(end($tab1))){
            $key = key($tab1);
            $tab1[$key] = $voie;

        }
        return $tab1;
    }

    public function moyenneVitesseParVoie(array $tabDeLaMemeVoie): array
    {
        $grdTab=null;
        $vitesseParVoie = [];
        $tailleTabCourant = 0;
        foreach ($tabDeLaMemeVoie as $voie => $listeDesPoints) {
            $vitesseDesUsagers = [];
            foreach ( $listeDesPoints as $item) {
                $taille = $tailleTabCourant;
                $tailleTabCourant = count($item);

               if($tailleTabCourant > $taille ){
                   $grdTab = $item;
               };
                $vitesseDesUsagers[]= $this->CalculvitesseMoyUsager($item);
            }
            $tailleTabCourant = 0;
            $vitesseParVoie[$voie]["moyenne"]= array_sum($vitesseDesUsagers) / count($vitesseDesUsagers);
            $vitesseParVoie[$voie]["points"]= $grdTab;

        }
        return $vitesseParVoie;
    }

    public function insertArraylist(PositionGeographique $pt): void
    {
        if ($pt !== null) {

            Singleton::getInstance()->addPoint($pt);
        }

    }

    public function insertTab(array $listPointsGeo, int $nbElements = 1000): ?array
    {
        /*
           //Vérifictaion de la taille de la liste
           $tailleListe = count($listPointsGeo);
           if($tailleListe < $nbElements)
           */
        //Initialisation du tableau à retourner
        $tabATraiter = array();
        //Récupération du 1er élément de la liste des points géographiques
        $premierElement = reset($listPointsGeo);
        //Récupération de l'index du permier élément
        $index = array_search($premierElement, $listPointsGeo);
        //Reconsitution du tableau à traiter

        for ($i = 0; $i < $nbElements; $i++) {
            //Insertion d'un élément de la liste des PG dans le tableau à traiter
            $tabATraiter[$i] = $listPointsGeo[$index + $i];
            //Suppression de l'élément dans la liste des PG
            unset($listPointsGeo[$i]);
        }
        return $tabATraiter;
    }

    private function compare(PositionGeographique $point1, PositionGeographique $point2)
    {
        if ($point1->getVoie()->getId() == $point2->getVoie()->getId()) {
            if ($point1->getUtilisateur()->getId() == $point2->getUtilisateur()->getId()) {
                if ($point1->getDatePosition() < $point2->getDatePosition()) {
                    return -1;
                }
                if ($point1->getDatePosition() > $point2->getDatePosition()) {
                    return 1;
                }
                return 0;
            } elseif ($point1->getUtilisateur()->getId() < $point2->getUtilisateur()->getId()) {
                return -1;
            } else {
                return 1;
            }

        } elseif ($point1->getVoie()->getId() < $point2->getVoie()->getId()) {
            return -1;
        } else {
            return 1;
        }



        if ($point1->getUtilisateur()->getId() == $point2->getUtilisateur()->getId()) {
            if ($point1->getDatePosition() < $point2->getDatePosition()) {
                return -1;
            }
            if ($point1->getDatePosition() > $point2->getDatePosition()) {
                return 1;
            }
            return 0;
        } elseif ($point1->getUtilisateur()->getId() < $point2->getUtilisateur()->getId()) {
            return -1;
        } else {
            return 1;
        }
    }

    public function triTab(array $tabATrier): array
    {
        usort($tabATrier, array($this, 'compare'));
        return $tabATrier;
    }

    public function attribVoieTabUsager(array &$tabUsagers) : array
    {
        //Boucle de formation des paires de tableaux d'usagers
        for ($i = 0; $i<count($tabUsagers); $i++)
        {
            for($j=$i+1; $j<count($tabUsagers); $j++)
            {
                $indexTab1 = $i;
//                $tab1 = $tabUsagers[$indexTab1];
                $indexTab2 = $j;
//                $tab2 = &$tabUsagers[$indexTab2];
                //appel de la fonction d'affection de voie aux tableaux d'usagers

                $this->affectationVoie($tabUsagers[$indexTab1], $tabUsagers[$indexTab2]);
            }

        }
//        dump($tabUsagers[$indexTab1], $tabUsagers[$indexTab2]);
        //Attribution de voie au tableau d'un usager qui serait isolé sur une voie
        foreach($tabUsagers as &$tabDunUsager)
        {

            if(end($tabDunUsager) == null)
            {
                //Récupération de l'id de l'usager comme nom de la voie
                $voie = $tabDunUsager[0]->getUtilisateur()->getId();
                $tabDunUsager[count($tabDunUsager)-1] = $voie;
            }
        }
    return $tabUsagers;
    }



    public function init()
    {
        //Création usager1
        $usager1 = new Utilisateur();
        $usager1->setId(1);
        $usager1->setNom("PAMAZI");
        $usager1->setPrenoms("Pierre");
        $usager1->setEmail("test@test.com");

        //Création usager2
        $usager2 = new Utilisateur();
        $usager2->setId(2);
        $usager2->setNom("GAGNON");
        $usager2->setPrenoms("Schamma");
        $usager2->setEmail("test2@test.com");

        //Création usager3
        $usager3 = new Utilisateur();
        $usager3->setId(3);
        $usager3->setNom("EDJAMTOLI");
        $usager3->setPrenoms("Céline");
        $usager3->setEmail("test3@test.com");

        //Création usager4
        $usager4 = new Utilisateur();
        $usager4->setId(4);
        $usager4->setNom("LAWSON");
        $usager4->setPrenoms("Matthieu");
        $usager4->setEmail("test4@test.com");

        //Création usager5
        $usager5 = new Utilisateur();
        $usager5->setId(5);
        $usager5->setNom("PAPA");
        $usager5->setPrenoms("Tiam");
        $usager5->setEmail("test5@test.com");

//        //Affichage de l'usager1
//        echo $usager1->getId().'<br/>';
//        echo $usager1->getEmail().'<br/>';
//        echo $usager1->getNom().'<br/>';
//        echo $usager1->getPrenoms().'<br/>';

        //Création du point1
        $point1 = new PositionGeographique();
        $point1->setId(1);
        $point1->setLatitude(6.175811);
        $point1->setLongitude(1.214421);
        $point1->setUtilisateur($usager1);
        $date1 = new \DateTime();
        $date1->setDate(2011, 3, 26);
        $date1->setTime(12, 23, 1);
        $point1->setDatePosition($date1);

        //Création du point2
        $point2 = new PositionGeographique();
        $point2->setId(2);
        $point2->setLatitude(6.175812);
        $point2->setLongitude(1.214422);
        $point2->setUtilisateur($usager2);
        $date2 = new \DateTime();
        $date2->setDate(2011, 3, 26);
        $date2->setTime(12, 23, 19);
        $point2->setDatePosition($date2);

        //Création du point3
        $point3 = new PositionGeographique();
        $point3->setId(3);
        $point3->setLatitude(6.175813);
        $point3->setLongitude(1.214423);
        $point3->setUtilisateur($usager3);
        $date3 = new \DateTime();
        $date3->setDate(2011, 3, 26);
        $date3->setTime(12, 23, 39);
        $point3->setDatePosition($date3);

        //Création du point4
        $point4 = new PositionGeographique();
        $point4->setId(4);
        $point4->setLatitude(6.175814);
        $point4->setLongitude(1.214424);
        $point4->setUtilisateur($usager4);
        $date4 = new \DateTime();
        $date4->setDate(2011, 3, 26);
        $date4->setTime(12, 23, 13);
        $point4->setDatePosition($date4);

        //Création du point5
        $point5 = new PositionGeographique();
        $point5->setId(5);
        $point5->setLatitude(6.175815);
        $point5->setLongitude(1.214425);
        $point5->setUtilisateur($usager5);
        $date5 = new \DateTime();
        $date5->setDate(2011, 3, 26);
        $date5->setTime(12, 24, 19);
        $point5->setDatePosition($date5);

        //Création du point6
        $point6 = new PositionGeographique();
        $point6->setId(6);
        $point6->setLatitude(6.175816);
        $point6->setLongitude(1.214426);
        $point6->setUtilisateur($usager1);
        $date6 = new \DateTime();
        $date6->setDate(2011, 3, 26);
        $date6->setTime(12, 23, 0);
        $point6->setDatePosition($date6);

//        echo $point1->getId().'<br/>';
//        echo $point1->getUtilisateur()->getNom().'<br/>';
        //Insertion des points dans la liste
        $listPoints[] = $point5;
        $listPoints[] = $point2;
        $listPoints[] = $point4;
        $listPoints[] = $point1;
        $listPoints[] = $point3;
        $listPoints[] = $point6;

        //print_r($listPoints[2]).'<br/>';
//        echo $listPoints[0]->getUtilisateur()->getId().'<br/>';
//        echo $listPoints[1]->getUtilisateur()->getId().'<br/>';
//        echo $listPoints[2]->getUtilisateur()->getId().'<br/>';
//        echo $listPoints[3]->getUtilisateur()->getId().'<br/>';
//        echo $listPoints[4]->getUtilisateur()->getId().'<br/>';
//        echo '<br/>';
        return $listPoints;


    }

    /**
     * Summary of tabUsagers
     * @param PositionGeographique[] $tabTrie
     * @param Utilisateur[] $listeUsers
     * @param array<Utilisateur,PositionGeographique[]|null>
     */
    public function tabUsagers($tabTrie, $listeUsers): array
    {

        $userPosition = [];
        $result = [];
        foreach ($listeUsers as $user) {
            $userPosition = $this->tabUsager($tabTrie, $user);
            array_push($result, $userPosition);
            $userPosition = [];
        }

        return $result;
    }

    public function toRadian($degree): float
    {
        return $degree * M_PI / 180;
    }

    public function calculDistance(PositionGeographique $point1, PositionGeographique $point2): float
    {


        $lon1 = $this->toRadian($point1->getLongitude());
        $lat1 = $this->toRadian($point1->getLatitude());
        $lon2 = $this->toRadian($point2->getLongitude());
        $lat2 = $this->toRadian($point2->getLatitude());

        $deltaLat = $lat2 - $lat1;
        $deltaLon = $lon2 - $lon1;

        $a = pow(sin($deltaLat / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($deltaLon / 2), 2);
        $c = 2 * asin(sqrt($a));
        $EARTH_RADIUS = 6371;
        $distance = $c * $EARTH_RADIUS * 1000;

        return $distance;
    }

    /**
     * Summary of calculVitesseIntantane
     * @param PositionGeographique $point1
     * @param PositionGeographique $point2
     */
    private function calculVitesseInstantane($point1, $point2, $dureeEnSeconde) : float
    {
        $vitesse = $this->calculDistance($point1, $point2) / $dureeEnSeconde;
        return $vitesse;
    }

    /**
     * Summary of VitesseMoyUsager
     * @param PositionGeographique[] $points
     * @param Utilisateur $user
     */
    public function CalculvitesseMoyUsager($points, int $duree=10): float
    {
        $nbre = count($points);
        $tabb = [];
        if ($nbre <=1 ){return 0.;}
        for ($n = 0; $n < $nbre - 1; $n++) {
            $tabb[$n] = $this->calculVitesseInstantane($points[$n], $points[$n + 1], $duree);
        }
        $vitesseMoy = array_sum($tabb) / count($tabb);
        return $vitesseMoy;
    }

    /**
     * Summary of tabUsager
     * @param PositionGeographique[] $tabTrie
     * @param Utilisateur $user
     */

    public function tabUsager($tabTrie, $user): array
    {
        $tab=[];
        foreach ($tabTrie as $position) {
            if ($position->getUtilisateur()->getId() === $user->getId()) {
                array_push($tab, $position);
            }
        }
        return $tab;
    }

    private function hasVoie(array $tab2)
    {
        return !is_null(end($tab2));
    }

    public function grouperTabUsagersParVoie(array $tabsAffectesVoies): array
    {
        //Initialisation du map
        $tabMap = [];
        //Initialisation du tableau des voies
        $tabVoies = [];

        //On récupère toutes les voies sans doublon dans un tableau des voies
        foreach($tabsAffectesVoies as $tab)
        {
            //On récupère la voie du tableau courant
            $voie = $tab[count($tab)-1];
            //Constitution du tableau des voies
            if(!(in_array($voie, $tabVoies)))
            {
                $tabVoies[] = $voie;
            }
        }
        //On regroupe les tableaux par voie
        foreach($tabVoies as $voie)
        {
            foreach($tabsAffectesVoies as $tabUsager)
            {
                if($tabUsager[count($tabUsager)-1] == $voie)
                {
                    array_pop($tabUsager);
                    $tabMap[$voie][] = $tabUsager;
                }
            }
        }
        return $tabMap;

    }

    public function getVitesseVoie(): array
    {
//        foreach ($points as $point) {
//            $this->insertArraylist($point);
//        }

        $echantillons = $this->positionGeographiqueRepository->getAllPoints();
        if(count($echantillons) < 10){
            return [];
        }
        $this->positionGeographiqueRepository->desactiverPoint($echantillons);
        // recperation des n premioer elemenys
//        $echantillons = $this->insertTab(Singleton::getInstance()->getListeDesPoints(), 90);


        // Tri de l'échantillon
//        $echantillonTries = $this->triTab($echantillons);
    
        $echantillonTries = $echantillons;

        //Liste des usagers de l'échantillon trié
        $usagers = $this->listUsagers($echantillonTries);

        // Les tableaux de points des usagers de l'échantillon
        $tabUsagers = $this->tabUsagers($echantillonTries, $usagers);

        // Initialisation des voies en NULL
        $tabUsagersNull = $this->initialisationVoies($tabUsagers);

        //Affectation de nom des voies
        $tabUsagersAvecVoies = $this->attribVoieTabUsager($tabUsagersNull);

        // Regroupement des tab usagers par voie
        $tabDeLaMemeVoie = $this->grouperTabUsagersParVoie($tabUsagersAvecVoies);

        return $this->moyenneVitesseParVoie($tabDeLaMemeVoie);
        
    }

    public function getVitesseVoie2(): array
    {
        $echantillons = $this->positionGeographiqueRepository->getAllPoints();

        $this->positionGeographiqueRepository->desactiverPoint($echantillons);
        // recperation des n premioer elemenys
//        $echantillons = $this->insertTab(Singleton::getInstance()->getListeDesPoints(), 90);


        // Tri de l'échantillon
        $echantillonTries = $this->triTab($echantillons);
//
        //Liste des usagers de l'échantillon trié
        $usagers = $this->listUsagers($echantillonTries);

        // Les tableaux de points des usagers de l'échantillon
        $tabUsagers = $this->tabUsagers($echantillonTries, $usagers);

        // Initialisation des voies en NULL
        $tabUsagersNull = $this->initialisationVoies($tabUsagers);

        //Affectation de nom des voies
        $tabUsagersAvecVoies = $this->attribVoieTabUsager($tabUsagersNull);

//        $tabUsagersAvecVoies = $this->attribVoieTabUsager2($tabUsagersNull);

        // Regroupement des tab usagers par voie
        $tabDeLaMemeVoie = $this->grouperTabUsagersParVoie($tabUsagersAvecVoies);

        return $this->moyenneVitesseParVoie($tabDeLaMemeVoie);
    }

    public function data() : array
    {
        $tab = [];
        $matrice = $this->getVitesseVoie();
        $i = 0;
        foreach($matrice as $voie)
        {

            $tab[$i][0] = $voie["moyenne"];
            $points = [];
            for($j = 0; $j < count($voie["points"]); $j++)
            {
                //Récupération de latitude + longitude de chaque point sous forme de tableau
                $point[0] = $voie["points"][$j]->getLatitude(); 
                $point[1] = $voie["points"][$j]->getLongitude(); 
                //Reconstitution du tableau des points de la voie $i avec uniquement lat et lon
                $points[] = $point;
                //Insertion des points de la voie $i dans tab[$i][1]
                //$tab[$i][1] = $points;
            }
            
            $tab[$i][1] = $points;
            $i++;
        }

        return $tab; 
    }

    public function data2() : array
    {
        $tab = [];
//        $matrice = $this->getVitesseVoie();
        $matrice = $this->getVitesseVoie2();
        $i = 0;
        foreach($matrice as $voie)
        {

            $tab[$i][0] = $voie["moyenne"];
            $points = [];
            for($j = 0; $j < count($voie["points"]); $j++)
            {
                //Récupération de latitude + longitude de chaque point sous forme de tableau
                $point[0] = $voie["points"][$j]->getLatitude();
                $point[1] = $voie["points"][$j]->getLongitude();
                //Reconstitution du tableau des points de la voie $i avec uniquement lat et lon
                $points[] = $point;
                //Insertion des points de la voie $i dans tab[$i][1]
                //$tab[$i][1] = $points;
            }

            $tab[$i][1] = $points;
            $i++;
        }

        return $tab;
    }
    
}