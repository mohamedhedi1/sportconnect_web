<?php

namespace App\Entity;

use App\Repository\ExerciceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ExerciceRepository::class)]
class Exercice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: "Le nom d'exercice ne peut pas être vide.")]
    #[Assert\Length(
        min: 7,
        minMessage: "Le nom d'exercice doit comporter au moins 7 caractères."
    )]
    #[ORM\Column(length: 255)]
    private ?string $nomExercice = null;

    #[ORM\Column(length: 255)]
    private ?string $imageExercice = null;

    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(
        value: 1,
        message: "La durée doit être d'au moins 1 minute."
    )]
    #[ORM\Column]
    private ?int $duration = null;

    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(
        value: 1,
        message: "La repetition doit être d'au moins 1 fois."
    )]
    #[ORM\Column]
    private ?int $repetation = null;

    #[ORM\ManyToOne(inversedBy: 'exercices')]
    private ?Equipement $equipement = null;

    #[ORM\ManyToMany(targetEntity: Serie::class, mappedBy: 'exercices')]
    private Collection $series;

    #[Assert\NotBlank(message: "L'instruction ne peut pas être vide.")]
    #[ORM\Column(length: 255)]
    private ?string $instruction = null;

    public function __construct()
    {
        $this->series = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomExercice(): ?string
    {
        return $this->nomExercice;
    }

    public function setNomExercice(string $nomExercice): self
    {
        $this->nomExercice = $nomExercice;

        return $this;
    }

    public function getImageExercice(): ?string
    {
        return $this->imageExercice;
    }

    public function setImageExercice(string $imageExercice): self
    {
        $this->imageExercice = $imageExercice;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getRepetation(): ?int
    {
        return $this->repetation;
    }

    public function setRepetation(int $repetation): self
    {
        $this->repetation = $repetation;

        return $this;
    }

    public function getEquipement(): ?Equipement
    {
        return $this->equipement;
    }

    public function setEquipement(?Equipement $equipement): self
    {
        $this->equipement = $equipement;

        return $this;
    }

    /**
     * @return Collection<int, Serie>
     */
    public function getSeries(): Collection
    {
        return $this->series;
    }

    public function addSeries(Serie $series): self
    {
        if (!$this->series->contains($series)) {
            $this->series->add($series);
            $series->addExercice($this);
        }

        return $this;
    }

    public function removeSeries(Serie $series): self
    {
        if ($this->series->removeElement($series)) {
            $series->removeExercice($this);
        }

        return $this;
    }

    public function getInstruction(): ?string
    {
        return $this->instruction;
    }

    public function setInstruction(string $instruction): self
    {
        $this->instruction = $instruction;

        return $this;
    }
}
