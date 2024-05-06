<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['category'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['film', 'category'])]
    private ?string $label = null;

    /**
     * @var Collection<int, FilmCategory>
     */
    #[ORM\OneToMany(targetEntity: FilmCategory::class, mappedBy: 'category', orphanRemoval: true)]
    // Do not add to group film or we get a circular reference error
    private Collection $filmCategories;

    public function __construct()
    {
        $this->filmCategories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Collection<int, FilmCategory>
     */
    public function getFilmCategories(): Collection
    {
        return $this->filmCategories;
    }

    public function addFilmCategory(FilmCategory $filmCategory): static
    {
        if (!$this->filmCategories->contains($filmCategory)) {
            $this->filmCategories->add($filmCategory);
            $filmCategory->setCategory($this);
        }

        return $this;
    }

    public function removeFilmCategory(FilmCategory $filmCategory): static
    {
        if ($this->filmCategories->removeElement($filmCategory)) {
            // set the owning side to null (unless already changed)
            if ($filmCategory->getCategory() === $this) {
                $filmCategory->setCategory(null);
            }
        }

        return $this;
    }
}
