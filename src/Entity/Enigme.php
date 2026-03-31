<?php

namespace App\Entity;

use App\Repository\EnigmeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'tbl_enigme')]
#[ORM\Entity(repositoryClass: EnigmeRepository::class)]
class Enigme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'enigmes')]
    private ?Type $type = null;

    #[ORM\Column]
    private ?int $ordre = null;

    #[ORM\Column(length: 50)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $consigne = null;

    #[ORM\Column(length: 50)]
    private ?string $codeSecret = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $codeReponse = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $choices = null;

    #[ORM\ManyToOne(inversedBy: 'enigmes')]
    private ?Vignette $vignette = null;

    #[ORM\ManyToOne(inversedBy: 'enigme')]
    private ?Jeu $jeu = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lien = null;

    #[ORM\Column(length: 255)]
    private ?string $solution = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $active = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeReponse(): ?string
    {
        return $this->codeReponse;
    }

    public function setCodeReponse(?string $codeReponse): static
    {
        $this->codeReponse = $codeReponse;

        return $this;
    }

    public function getChoices(): ?array
    {
        return $this->choices;
    }

    public function setChoices(?array $choices): static
    {
        $this->choices = $choices;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): static
    {
        $this->ordre = $ordre;

        return $this;
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

    public function getConsigne(): ?string
    {
        return $this->consigne;
    }

    public function setConsigne(string $consigne): static
    {
        $this->consigne = $consigne;

        return $this;
    }

    public function getCodeSecret(): ?string
    {
        return $this->codeSecret;
    }

    public function setCodeSecret(string $codeSecret): static
    {
        $this->codeSecret = $codeSecret;

        return $this;
    }

    public function getVignette(): ?Vignette
    {
        return $this->vignette;
    }

    public function setVignette(?Vignette $vignette): static
    {
        $this->vignette = $vignette;

        return $this;
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

    public function getLien(): ?string
    {
        return $this->lien;
    }

    public function setLien(?string $lien): static
    {
        $this->lien = $lien;

        return $this;
    }

    public function getSolution(): ?string
    {
        return $this->solution;
    }

    public function setSolution(string $solution): static
    {
        $this->solution = $solution;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }
}
