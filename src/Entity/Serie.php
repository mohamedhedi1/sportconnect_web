<?php

namespace App\Entity;

use App\Repository\SerieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SerieRepository::class)]
class Serie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: "Le titre ne peut pas être vide.")]
    #[Assert\Length(
        min: 5,
        minMessage: "Le titre doit comporter au moins 5 caractères."
    )]
    #[ORM\Column(length: 255)]
    private ?string $titreSerie = null;

    #[ORM\Column(length: 255)]
    private ?string $imageSerie = null;

    #[ORM\ManyToMany(targetEntity: Exercice::class, inversedBy: 'series')]
    private Collection $exercices;

    #[ORM\Column(nullable: true)]
    private ?int $valeur = null;

    public function __construct()
    {
        $this->exercices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitreSerie(): ?string
    {
        return $this->titreSerie;
    }

    public function setTitreSerie(string $titreSerie): self
    {
        $this->titreSerie = $titreSerie;

        return $this;
    }

    public function getImageSerie(): ?string
    {
        return $this->imageSerie;
    }

    public function setImageSerie(string $imageSerie): self
    {
        $this->imageSerie = $imageSerie;

        return $this;
    }

    /**
     * @return Collection<int, Exercice>
     */
    public function getExercices(): Collection
    {
        return $this->exercices;
    }

    public function addExercice(Exercice $exercice): self
    {
        if (!$this->exercices->contains($exercice)) {
            $this->exercices->add($exercice);
        }

        return $this;
    }

    public function removeExercice(Exercice $exercice): self
    {
        $this->exercices->removeElement($exercice);

        return $this;
    }

    public function getValeur(): ?int
    {
        return $this->valeur;
    }

    public function setValeur(?int $valeur): self
    {
        $this->valeur = $valeur;

        return $this;
    }
}
