<?php

namespace App\Utils;

use App\Entity\PositionGeographique;

class Utilitaire
{
    public static function  random_float($min,$max){
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);


    }
}