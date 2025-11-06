<?php

namespace App\Entity;

use App\Repository\EnigmeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnigmeRepository::class)]
class Enigme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
/**
     * @var Collection<int, Type>
     */
    #[ORM\ManyToMany(targetEntity: Type::class, inversedBy: 'enigmes')]

    private Collection $type;
    #[ORM\Column]
    private ?int $ordre = null;

    #[ORM\Column(length: 50)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $consigne = null;

    #[ORM\Column(length: 50)]
    private ?string $codeSecret = null;

    #[ORM\OneToOne(inversedBy: 'enigme', cascade: ['persist', 'remove'])]
    private ?Vignette $vignette = null;

    public function __construct()
    {
        $this->type = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Type>
     */
    public function getType(): Collection
    {
        return $this->type;
    }

    public function addType(Type $type): static
    {
        if (!$this->type->contains($type)) {
            $this->type->add($type);
        }

        return $this;
    }

    public function removeType(Type $type): static
    {
        $this->type->removeElement($type);

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

    
}
