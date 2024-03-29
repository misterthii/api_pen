<?php

namespace App\Entity;

use App\Repository\PenRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: PenRepository::class)]
class Pen
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Groups('pens:read')] // <== Add this line pour ajouter le champ name dans la réponse de l'API
    private ?string $name = null;

    #[ORM\Column]
    #[Groups('pens:read')]
    private ?float $price = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups('pens:read')]
    private ?string $description = null;

    #[ORM\Column(length: 10, unique: true, nullable: true)]
    #[Groups('pens:read')]
    private ?string $ref = null;

    #[ORM\ManyToOne(inversedBy: 'pens')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('pens:read')]
    private ?Type $type = null;

    #[ORM\ManyToOne(inversedBy: 'pens')]
    #[Groups('pens:read')]
    private ?Material $material = null;

    #[ORM\ManyToMany(targetEntity: Color::class, inversedBy: 'pens', cascade:["persist"])]
    #[Groups('pens:read','pens:create')]
    private Collection $colors;

    public function __construct()
    {
        $this->colors = new ArrayCollection();
    }

    #[ORM\ManyToOne(inversedBy: 'pens')]
    #[Groups('pens:read')]
    private ?Brand $brand = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(string $ref): static
    {
        $this->ref = $ref;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getMaterial(): ?Material
    {
        return $this->material;
    }


    public function setMaterial(?Material $material): static
    {
        $this->material = $material;

        return $this;
    }

    public function getColors(): Collection
    {
        return $this->colors;
    }

    public function setColor(?Color $color): static
    {
        $this->colors = $color;

        return $this;
    }

    public function addColor(Color $color): static
    {
        if (!$this->colors->contains($color)) {
            $this->colors->add($color);
            $color->addPen($this);
        }

        return $this;
    }

    public function removeColor(Color $color): static
    {
        if ($this->colors->removeElement($color)) {
            $color->removePen($this);
        }

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): static
    {
        $this->brand = $brand;

        return $this;
    }
}