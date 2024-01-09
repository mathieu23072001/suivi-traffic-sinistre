<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;



#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]

    private ?string $email = null;

   

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 8)]

    private ?string $telephone = null;

    #[ORM\Column(length: 127)]

    private ?string $nom = null;

    #[ORM\Column(length: 255)]

    
    private ?string $prenoms = null;

    #[ORM\ManyToMany(targetEntity: Profil::class)]
    private Collection $profils;

    #[ORM\Column(nullable: true)]
    private bool $abonnementActif = true;

    #[ORM\OneToMany(mappedBy: 'utilisateurs', targetEntity: Comments::class, orphanRemoval: true)]
    private Collection $comments;


    public function __construct()
    {
        $this->profils = new ArrayCollection();
        $this->abonnementActif = true;
        $this->comments = new ArrayCollection();
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = [];
        $profils = $this->getProfils();
        foreach ($profils as $p){
            $roles[] = $p->getCode(); 
        }
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

   

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenoms(): ?string
    {
        return $this->prenoms;
    }

    public function setPrenoms(string $prenoms): self
    {
        $this->prenoms = $prenoms;

        return $this;
    }

    /**
     * @return Collection<int, Profil>
     */
    public function getProfils(): Collection
    {
        return $this->profils;
    }

    public function addProfil(Profil $profil): self
    {
        if (!$this->profils->contains($profil)) {
            $this->profils->add($profil);
        }

        return $this;
    }

    public function removeProfil(Profil $profil): self
    {
        $this->profils->removeElement($profil);

        return $this;
    }

    public function getNomPrenoms(): ?string
    {
        return $this->getNom()." ".$this->getPrenoms();
    }

    public function isAbonnementActif(): bool
    {
        return $this->abonnementActif;
    }

    public function setAbonnementActif(bool $abonnementActif): self
    {
        $this->abonnementActif = $abonnementActif;

        return $this;
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
            $comment->setUtilisateurs($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUtilisateurs() === $this) {
                $comment->setUtilisateurs(null);
            }
        }

        return $this;
    }
}
