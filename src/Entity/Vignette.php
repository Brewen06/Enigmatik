<?php

namespace App\Entity;

use App\Repository\VignetteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VignetteRepository::class)]
class Vignette
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $information = null;

    #[ORM\OneToOne(mappedBy: 'vignette', cascade: ['persist', 'remove'])]
    private ?Enigme $enigme = null;

   
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getInformation(): ?string
    {
        return $this->information;
    }

    public function setInformation(string $information): static
    {
        $this->information = $information;

        return $this;
    }

    public function getEnigme(): ?Enigme
    {
        return $this->enigme;
    }

    public function setEnigme(?Enigme $enigme): static
    {
        // unset the owning side of the relation if necessary
        if ($enigme === null && $this->enigme !== null) {
            $this->enigme->setVignette(null);
        }

        // set the owning side of the relation if necessary
        if ($enigme !== null && $enigme->getVignette() !== $this) {
            $enigme->setVignette($this);
        }

        $this->enigme = $enigme;

        return $this;
    }

    
}
