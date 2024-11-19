<?php

namespace App\Entity;

use App\Entity\Categorie;
use App\Entity\Association;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ActionRepository;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: ActionRepository::class)]
class Action
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nameAction = null;

    #[ORM\Column]
    private ?string $indexAction = null;

    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: 'actions')]
    #[ORM\JoinColumn(nullable: false)]
    private $categorie;


    #[ORM\ManyToMany(targetEntity: Association::class, mappedBy: 'actions')]
    private $associations;

    public function __construct()
    {
        $this->associations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameAction(): ?string
    {
        return $this->nameAction;
    }

    public function setNameAction(string $nameAction): static
    {
        $this->nameAction = $nameAction;

        return $this;
    }

    public function getIndex(): ?string
    {
        return $this->indexAction;
    }

    public function setIndex(string $indexAction): static
    {
        $this->indexAction = $indexAction;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getAssociations(): ArrayCollection
    {
        return $this->associations;
    }
}
