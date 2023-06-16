<?php

namespace App\Entity;

use App\Repository\RepasEntityRepository;
use App\Entity\IngredientEntity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RepasEntityRepository::class)]
class RepasEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    #[ORM\Column(type: Types::TEXT)]

    private ?string $Description = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    
    private ?\DateTimeInterface $Heure = null;

    #[ORM\Column(length: 255)]

    private ?string $Image = null;

    #[ORM\Column]

    private ?int $Calories = null;

    #[ORM\ManyToMany(targetEntity: IngredientEntity::class, inversedBy: 'repas')]
    private Collection $ingredients;

    public function __construct()
    {
        $this->ingredients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    public function getHeure(): ?\DateTimeInterface
    {
        return $this->Heure;
    }

    public function setHeure(\DateTimeInterface $Heure): self
    {
        $this->Heure = $Heure;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->Image;
    }

    public function setImage(string $Image): self
    {
        $this->Image = $Image;

        return $this;
    }

    public function getCalories(): ?int
    {
        return $this->Calories;
    }

    public function setCalories(int $Calories): self
    {
        $this->Calories = $Calories;

        return $this;
    }

    /**
     * @return Collection<int, IngredientEntity>
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }
    public function setIngredients(Collection $ingredients): self
    {
        $this->ingredients = $ingredients;

        return $this;
    }

    public function addIngredient(IngredientEntity $ingredient): self
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients->add($ingredient);
        }

        return $this;
    }

    public function removeIngredient(IngredientEntity $ingredient): self
    {
        if ($this->ingredients->contains($ingredient)) {
            $this->ingredients->removeElement($ingredient);
        }

        return $this;
    }
    
}
