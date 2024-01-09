<?php

namespace App\Entity;

use App\Repository\SinistreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SinistreRepository::class)]
class Sinistre extends Declaration
{
   

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeSinistre $typeSinistre = null;


    public function getTypeSinistre(): ?TypeSinistre
    {
        return $this->typeSinistre;
    }

    public function setTypeSinistre(?TypeSinistre $typeSinistre): self
    {
        $this->typeSinistre = $typeSinistre;

        return $this;
    }

    public function getType()
    {
        return "Sinistre";
    }
    public function getLibelleDeclaration() {
        return  $this->getTypeSinistre()->getLibelle() ." à ". $this->getLieu();

    }
}
