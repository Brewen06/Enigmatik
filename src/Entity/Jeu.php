<?php

namespace App\Entity;

use App\Entity\Enigme;
use App\Entity\Parametre;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\JeuRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: 'tbl_jeu')]
#[ORM\Entity(repositoryClass: JeuRepository::class)]
class Jeu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $messageDeBienvenue = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageBienvenue = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $codeFinal = null;

    /**
     * @var Collection<int, Parametre>
     */
    #[ORM\OneToMany(targetEntity: Parametre::class, mappedBy: 'jeu')]
    private Collection $parametres;

    /**
     * @var Collection<int, Enigme>
     */
    #[ORM\OneToMany(targetEntity: Enigme::class, mappedBy: 'jeu')]
    private Collection $enigme;

    public function __construct()
    {
        $this->parametres = new ArrayCollection();
        $this->enigme = new ArrayCollection();
    }

    

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getMessageDeBienvenue(): ?string
    {
        return $this->messageDeBienvenue;
    }

    public function setMessageDeBienvenue(?string $messageDeBienvenue): static
    {
        $this->messageDeBienvenue = $messageDeBienvenue;

        return $this;
    }

    public function getImageBienvenue(): ?string
    {
        return $this->imageBienvenue;
    }

    public function setImageBienvenue(?string $imageBienvenue): static
    {
        $this->imageBienvenue = $imageBienvenue;

        return $this;
    }

    public function getCodeFinal(): ?string
    {
        return $this->codeFinal;
    }

    public function setCodeFinal(?string $codeFinal): static
    {
        $this->codeFinal = $codeFinal;

        return $this;
    }

    /**
     * @return Collection<int, Parametre>
     */
    public function getParametres(): Collection
    {
        return $this->parametres;
    }

    public function addParametre(Parametre $parametre): static
    {
        if (!$this->parametres->contains($parametre)) {
            $this->parametres->add($parametre);
            $parametre->setJeu($this);
        }

        return $this;
    }

    public function removeParametre(Parametre $parametre): static
    {
        if ($this->parametres->removeElement($parametre)) {
            // set the owning side to null (unless already changed)
            if ($parametre->getJeu() === $this) {
                $parametre->setJeu(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Enigme>
     */
    public function getEnigme(): Collection
    {
        return $this->enigme;
    }

    public function addEnigme(Enigme $enigme): static
    {
        if (!$this->enigme->contains($enigme)) {
            $this->enigme->add($enigme);
            $enigme->setJeu($this);
        }

        return $this;
    }

    public function removeEnigme(Enigme $enigme): static
    {
        if ($this->enigme->removeElement($enigme)) {
            // set the owning side to null (unless already changed)
            if ($enigme->getJeu() === $this) {
                $enigme->setJeu(null);
            }
        }

        return $this;
    }

    
}
