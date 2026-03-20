<?php

namespace App\Entity;

use App\Repository\ParametreRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParametreRepository::class)]
class Parametre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'parametres')]
    private ?Jeu $jeu = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(length: 255)]
    private ?string $valeur = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $choix = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJeu(): ?Jeu
    {
        return $this->jeu;
    }

    public function setJeu(?Jeu $jeu): static
    {
        $this->jeu = $jeu;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getValeur(): ?string
    {
        return $this->valeur;
    }

    public function setValeur(string $valeur): static
    {
        $this->valeur = $valeur;

        return $this;
    }

    public function getChoix(): array
    {
        return $this->choix;
    }

    public function setChoix(array $choix): static
    {
        $this->choix = $choix;

        return $this;
    }
}
