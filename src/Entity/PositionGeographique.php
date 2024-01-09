<?php

namespace App\Entity;

use App\Repository\PositionGeographiqueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity(repositoryClass: PositionGeographiqueRepository::class)]
class PositionGeographique
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $latitude = null;

    #[ORM\Column]
    private ?float $longitude = null;

    #[ORM\ManyToOne()]
    #[ORM\JoinColumn(nullable: false)]
    #[Ignore]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datePosition = null;

    #[ORM\Column(nullable: true)]
    private ?bool $actif = null;

    #[ORM\ManyToOne]
    private ?Voie $voie = null;

    /**
     * @param bool|null $actif
     */
    public function __construct()
    {
        $this->actif = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getDatePosition(): ?\DateTimeInterface
    {
        return $this->datePosition;
    }

    public function setDatePosition(?\DateTimeInterface $datePosition): self
    {
        $this->datePosition = $datePosition;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(?bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getVoie(): ?Voie
    {
        return $this->voie;
    }

    public function setVoie(?Voie $voie): self
    {
        $this->voie = $voie;

        return $this;
    }

}
