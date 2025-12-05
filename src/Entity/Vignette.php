<?php

namespace App\Entity;

use App\Repository\VignetteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'tbl_vignette')]
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

    #[ORM\OneToMany(mappedBy: 'vignette', targetEntity: Enigme::class)]
    private Collection $enigmes;

    public function __construct()
    {
        $this->enigmes = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Enigme>
     */
    public function getEnigmes(): Collection
    {
        return $this->enigmes;
    }

    public function addEnigme(Enigme $enigme): static
    {
        if (!$this->enigmes->contains($enigme)) {
            $this->enigmes->add($enigme);
            $enigme->setVignette($this);
        }

        return $this;
    }

    public function removeEnigme(Enigme $enigme): static
    {
        if ($this->enigmes->removeElement($enigme)) {
            // set the owning side to null (unless already changed)
            if ($enigme->getVignette() === $this) {
                $enigme->setVignette(null);
            }
        }

        return $this;
    }

    
}
