<?php

namespace App\Entity;

use App\Repository\InterisseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InterisseRepository::class)]
class Interisse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $idClient = null;

    #[ORM\Column(length: 255)]
    private ?string $idSerie = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdClient(): ?string
    {
        return $this->idClient;
    }

    public function setIdClient(string $idClient): self
    {
        $this->idClient = $idClient;

        return $this;
    }

    public function getIdSerie(): ?string
    {
        return $this->idSerie;
    }

    public function setIdSerie(string $idSerie): self
    {
        $this->idSerie = $idSerie;

        return $this;
    }
}
