<?php

namespace App\Entity;

use App\Repository\EquipeRepository;
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

    #[ORM\Column(nullable: true)]
    private ?int $enigmeActuelle = null;

    #[ORM\ManyToOne(targetEntity: Avatar::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Avatar $avatar = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $startedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $finishedAt = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $enigmesResolues = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(?\DateTimeInterface $startedAt): static
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getFinishedAt(): ?\DateTimeInterface
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(?\DateTimeInterface $finishedAt): static
    {
        $this->finishedAt = $finishedAt;

        return $this;
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

    public function getEnigmeActuelle(): ?int
    {
        return $this->enigmeActuelle;
    }

    public function setEnigmeActuelle(?int $enigmeActuelle): static
    {
        $this->enigmeActuelle = $enigmeActuelle;

        return $this;
    }

    public function getAvatar(): ?Avatar
    {
        return $this->avatar;
    }

    public function setAvatar(?Avatar $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return list<int>
     */
    public function getEnigmesResolues(): array
    {
        $values = $this->enigmesResolues ?? [];
        $values = array_map(static fn(mixed $value): int => (int) $value, $values);
        $values = array_values(array_unique(array_filter($values, static fn(int $value): bool => $value > 0)));
        sort($values);

        return $values;
    }

    /**
     * @param array<mixed>|null $enigmesResolues
     */
    public function setEnigmesResolues(?array $enigmesResolues): static
    {
        $this->enigmesResolues = $enigmesResolues ?? [];

        return $this;
    }

    public function addEnigmeResolue(int $enigmeId): bool
    {
        if ($enigmeId <= 0) {
            return false;
        }

        $resolved = $this->getEnigmesResolues();

        if (in_array($enigmeId, $resolved, true)) {
            return false;
        }

        $resolved[] = $enigmeId;
        sort($resolved);
        $this->enigmesResolues = $resolved;

        return true;
    }

    public function hasEnigmeResolue(int $enigmeId): bool
    {
        return in_array($enigmeId, $this->getEnigmesResolues(), true);
    }

    public function getNombreEnigmesResolues(): int
    {
        return count($this->getEnigmesResolues());
    }

    public function __toString(): string
    {
        return $this->nom ?? 'Equipe';
    }
}
