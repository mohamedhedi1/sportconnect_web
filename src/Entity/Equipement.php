<?php

namespace App\Entity;

use App\Repository\EquipementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EquipementRepository::class)]
class Equipement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: "Le nom d'équipement ne peut pas être vide.")]
    #[Assert\Length(
        min: 7,
        minMessage: "Le nom d'équipement doit comporter au moins 7 caractères."
    )]
    #[ORM\Column(length: 255)]
    private ?string $nomEquipement = null;

    #[ORM\Column(length: 255)]
    private ?string $imageEquipement = null;

    #[ORM\OneToMany(mappedBy: 'equipement', targetEntity: Exercice::class)]
    private Collection $exercices;

    public function __construct()
    {
        $this->exercices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomEquipement(): ?string
    {
        return $this->nomEquipement;
    }

    public function setNomEquipement(string $nomEquipement): self
    {
        $this->nomEquipement = $nomEquipement;

        return $this;
    }

    public function getImageEquipement(): ?string
    {
        return $this->imageEquipement;
    }

    public function setImageEquipement(string $imageEquipement): self
    {
        $this->imageEquipement = $imageEquipement;

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
            $exercice->setEquipement($this);
        }

        return $this;
    }

    public function removeExercice(Exercice $exercice): self
    {
        if ($this->exercices->removeElement($exercice)) {
            // set the owning side to null (unless already changed)
            if ($exercice->getEquipement() === $this) {
                $exercice->setEquipement(null);
            }
        }

        return $this;
    }
}
