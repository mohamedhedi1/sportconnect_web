<?php

namespace App\Entity;

use App\Repository\IngredientEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IngredientEntityRepository::class)]
class IngredientEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    /*#[ORM\Column(type: Types::TEXT)]
    private ?string $Description = null;*/

    #[ORM\Column(length:100, nullable:true)]
    private ?int $Quantite = null;

    #[ORM\ManyToMany(targetEntity: RepasEntity::class, mappedBy: 'ingredients')]
    private Collection $repas;

    #[ORM\Column]
    private ?int $Calories = null;

    public function __construct()
    {
        $this->repas = new ArrayCollection();
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

   /* public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }*/

    public function getQuantite(): ?int
    {
        return $this->Quantite;
    }

    public function setQuantite(int $Quantite): self
    {
        $this->Quantite = $Quantite;

        return $this;
    }

    /**
     * @return Collection<int, RepasEntity>
     */
    public function getRepas(): Collection
    {
        return $this->repas;
    }

    public function addRepa(RepasEntity $repa): self
    {
        if (!$this->repas->contains($repa)) {
            $this->repas->add($repa);
            $repa->addIngredient($this);
        }

        return $this;
    }

    public function removeRepa(RepasEntity $repa): self
    {
        if ($this->repas->removeElement($repa)) {
            $repa->removeIngredient($this);
        }

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
    
}
