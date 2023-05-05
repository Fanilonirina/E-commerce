<?php

namespace App\Entity;

use App\Entity\Trait\CreatedAtTrait;
use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{

    use CreatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le nom de la categorie ne peut pas être vide! ')]
    #[Assert\Length(
        max: 50,
        maxMessage: 'Le nom du categorie ne peut pas depassé du 50 caractères! '
    )]
    private ?string $name_categorie = null;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: Produit::class)]
    private Collection $produits;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
        $this->produits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameCategorie(): ?string
    {
        return $this->name_categorie;
    }

    public function setNameCategorie(string $name_categorie): self
    {
        $this->name_categorie = $name_categorie;

        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): self
    {
        if (!$this->produits->contains($produit)) {
            $this->produits->add($produit);
            $produit->setCategorie($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): self
    {
        if ($this->produits->removeElement($produit)) {
            // set the owning side to null (unless already changed)
            if ($produit->getCategorie() === $this) {
                $produit->setCategorie(null);
            }
        }

        return $this;
    }
}
