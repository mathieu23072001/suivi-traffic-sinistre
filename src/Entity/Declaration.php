<?php

namespace App\Entity;

use App\Repository\DeclarationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DeclarationRepository::class)]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name :"dtype", type:"string")]
#[ORM\DiscriminatorMap(["SIN" => Sinistre::class, "INFO" => InformationUtile::class])]
class Declaration
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected ?\DateTimeInterface $dateDeclaration = null;

    #[ORM\Column(length: 255)]
    protected ?string $libelle = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $description = null;

    #[ORM\Column(length: 255)]
    protected ?string $lieu = null;

    #[ORM\Column]
    protected ?bool $published = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?\DateTimeInterface $datePublication = null;

    #[ORM\OneToMany(mappedBy: 'declaration', targetEntity: Image::class, orphanRemoval: true, cascade:['persist'])]
    protected Collection $images;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    protected ?Utilisateur $utilisateur = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateFinPublication = null;

    #[ORM\Column]
    private ?float $longitude = null;

    #[ORM\Column(nullable: true)]
    private ?float $latitude = null;

    #[ORM\ManyToMany(targetEntity: Utilisateur::class)]
    #[JoinTable('user_declaration_like')]
    private Collection $likes;

    #[ORM\ManyToMany(targetEntity: Utilisateur::class)]
    #[JoinTable('user_declaration_dislike')]
    private Collection $dislikes;

    #[ORM\OneToMany(mappedBy: 'declarations', targetEntity: Comments::class, cascade: ["persist"])]
    private Collection $comments;


    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->dateDeclaration = new \DateTime();
        $this->likes = new ArrayCollection();
        $this->dislikes = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }
    public function getClassName()
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDeclaration(): ?\DateTimeInterface
    {
        return $this->dateDeclaration;
    }

    public function setDateDeclaration(\DateTimeInterface $dateDeclaration): self
    {
        $this->dateDeclaration = $dateDeclaration;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }


    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function getDislikes(): Collection
    {
        return $this->dislikes;
    }

    public function addLike(Utilisateur $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
        }

        return $this;
    }

    public function addDislike(Utilisateur $dislike): self
    {
        if (!$this->dislikes->contains($dislike)) {
            $this->dislikes[] = $dislike;
        }

        return $this;
    }

    public function removeLike(Utilisateur $like): self
    {
        $this->likes->removeElement($like);

        return $this;
    }

    public function removeDislike(Utilisateur $dislike): self
    {
        $this->dislikes->removeElement($dislike);

        return $this;
    }

    public function getDatePublication(): ?\DateTimeInterface
    {
        return $this->datePublication;
    }

    public function setDatePublication(\DateTimeInterface $datePublication): self
    {
        $this->datePublication = $datePublication;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function getFirstImage() {
        $tab = $this->getImages()->toArray();
        if(empty($tab)){
            return null;
        }
        return $tab[0];
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setDeclaration($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getDeclaration() === $this) {
                $image->setDeclaration(null);
            }
        }

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

    public function getEtat(): string{
        if($this->isPublished() && !is_null($this->datePublication)) return "Publié";

        if(!$this->isPublished() && is_null($this->datePublication)) return "Non Publié";

        return "Expiré";
    }

    public function getDateFinPublication(): ?\DateTimeInterface
    {
        return $this->dateFinPublication;
    }

    public function setDateFinPublication(?\DateTimeInterface $dateFinPublication): self
    {
        $this->dateFinPublication = $dateFinPublication;
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

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function isLikedByUser(Utilisateur $user): bool
    {
        return $this->likes->contains($user);
    }

    public function isDislikedByUser(Utilisateur $user): bool
    {
        return $this->dislikes->contains($user);
    }

    /**
     * Get the number of likes 
     *
     * @return integer
     */
    public function howManyLikes(): int
    {
        return count($this->likes);
    }


    /**
     * Get the number of dislikes 
     *
     * @return integer
     */
    public function howManyDislikes(): int
    {
        return count($this->dislikes);
    }

    /**
     * @return Collection<int, Comments>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setDeclarations($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getDeclarations() === $this) {
                $comment->setDeclarations(null);
            }
        }

        return $this;
    }

}
