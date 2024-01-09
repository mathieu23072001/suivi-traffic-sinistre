<?php

namespace App\Entity;

use App\Repository\VoieRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoieRepository::class)]
class Voie
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::BIGINT, unique: true)]
    private ?string $idOSM = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdOSM(): ?string
    {
        return $this->idOSM;
    }

    public function setIdOSM(string $idOSM): self
    {
        $this->idOSM = $idOSM;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }
}
