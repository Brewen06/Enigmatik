<?php

namespace App\Entity;

use App\Repository\TypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'tbl_type')]
#[ORM\Entity(repositoryClass: TypeRepository::class)]
class Type
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $libelle = null;

    /**
     * @var Collection<int, Enigme>
     */
    #[ORM\OneToMany(targetEntity: Enigme::class, mappedBy: 'type')]
    private Collection $enigmes;

    public function __construct()
    {
        $this->enigmes = new ArrayCollection();
    }

    /**
     * @var Collection<int, Enigme>
     */
    #[ORM\ManyToMany(targetEntity: Enigme::class, mappedBy: 'type')]
    

    public function getId(): ?int
    {
        return $this->id;
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
            $enigme->setType($this);
        }

        return $this;
    }

    public function removeEnigme(Enigme $enigme): static
    {
        if ($this->enigmes->removeElement($enigme)) {
            // set the owning side to null (unless already changed)
            if ($enigme->getType() === $this) {
                $enigme->setType(null);
            }
        }

        return $this;
    }

}
