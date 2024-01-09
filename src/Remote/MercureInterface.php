<?php

namespace App\Remote;

use App\Entity\Sinistre;

interface MercureInterface
{

    public function publishUnSinistre();
    public function getDeclarationToPublish();
}