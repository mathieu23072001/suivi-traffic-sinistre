<?php

namespace App\Utils;

use App\Entity\PositionGeographique;

class Singleton
{
    /**
     * @var Singleton
     * @access private
     * @static
     */
//    private static $_instance = null;
    private static $instances = [];
    private array $listeDesPoints;

    /**
     * Constructeur de la classe
     *
     * @param void
     * @return void
     */
    protected function __construct() {
        $this->listeDesPoints = [];
    }
    protected function __clone() { }

    /**
     * Singletons should not be restorable from strings.
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    /**
     * Méthode qui crée l'unique instance de la classe
     * si elle n'existe pas encore puis la retourne.
     *
     * @param void
     * @return Singleton
     */
    public static function getInstance() {

//        if(is_null(self::$_instance)) {
//            self::$_instance = new Singleton();
//            dump("creation nouvel objet");
//        }
//
//        return self::$_instance;

        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }
        return self::$instances[$cls];
    }

    /**
     * @return array
     */
    public function getListeDesPoints(): array
    {
        return $this->listeDesPoints;
    }

    /**
     * @param array $listeDesPoints
     */
    public function setListeDesPoints(array $listeDesPoints): void
    {
        $this->listeDesPoints = $listeDesPoints;
    }

    public function addPoint(PositionGeographique $point){
        $liste = $this->getListeDesPoints();
        array_push($liste,$point);
        $this->setListeDesPoints($liste);
    }
}