<?php

namespace App\Entity;

use App\Repository\InformationUtileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InformationUtileRepository::class)]
class InformationUtile extends Declaration
{

    public function getType()
    {
        return "Info. utile";
    }
    public function getLibelleDeclaration() {
       return  $this->getLibelle() ." à ". $this->getLieu();

    }
   
}
