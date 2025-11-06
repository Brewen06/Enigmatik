<?php

namespace App\Entity;

use App\Repository\EquipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'tbl_equipe')]
#[ORM\Entity(repositoryClass: EquipeRepository::class)]
class Equipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?int $position = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $note = null;

    #[ORM\Column(nullable: true)]
    private ?int $enigmeActuelle = null;

    /**
     * @var Collection<int, Avatar>
     */
    #[ORM\OneToMany(targetEntity: Avatar::class, mappedBy: 'equipe')]
    private Collection $avatar;

    public function __construct()
    {
        $this->avatar = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getEnigmeActuelle(): ?int
    {
        return $this->enigmeActuelle;
    }

    public function setEnigmeActuelle(?int $enigmeActuelle): static
    {
        $this->enigmeActuelle = $enigmeActuelle;

        return $this;
    }

    /**
     * @return Collection<int, Avatar>
     */
    public function getAvatar(): Collection
    {
        return $this->avatar;
    }

    public function addAvatar(Avatar $avatar): static
    {
        if (!$this->avatar->contains($avatar)) {
            $this->avatar->add($avatar);
            $avatar->setEquipe($this);
        }

        return $this;
    }

    public function removeAvatar(Avatar $avatar): static
    {
        if ($this->avatar->removeElement($avatar)) {
            // set the owning side to null (unless already changed)
            if ($avatar->getEquipe() === $this) {
                $avatar->setEquipe(null);
            }
        }

        return $this;
    }
}
