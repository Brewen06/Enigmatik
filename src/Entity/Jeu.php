<?php

namespace App\Entity;

use App\Repository\JeuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

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

    /**
     * @var Collection<int, Parametre>
     */
    #[ORM\OneToMany(targetEntity: Parametre::class, mappedBy: 'jeu')]
    private Collection $parametres;

    public function __construct()
    {
        $this->parametres = new ArrayCollection();
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

    
}
