<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\FilmRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FilmRepository::class)]
class Film
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['film'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['film'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['film'])]
    private ?string $summary = null;

    #[ORM\Column]
    #[Groups(['film'])]
    private ?int $releaseYear = null;

    #[ORM\Column(length: 255)]
    #[Groups(['film'])]
    private ?string $realisator = null;


    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'film', orphanRemoval: true)]
    private Collection $comments;

    /**
     * @var Collection<int, FilmCategory>
     */
    #[ORM\OneToMany(targetEntity: FilmCategory::class, mappedBy: 'film', orphanRemoval: true)]
    #[Groups(['film'])]
    private Collection $filmCategories;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->filmCategories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): static
    {
        $this->summary = $summary;

        return $this;
    }

    public function getReleaseYear(): ?int
    {
        return $this->releaseYear;
    }

    public function setReleaseYear(int $releaseYear): static
    {
        $this->releaseYear = $releaseYear;

        return $this;
    }

    public function getRealisator(): ?string
    {
        return $this->realisator;
    }

    public function setRealisator(string $realisator): static
    {
        $this->realisator = $realisator;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setFilm($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getFilm() === $this) {
                $comment->setFilm(null);
            }
        }

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
            $filmCategory->setFilm($this);
        }

        return $this;
    }

    public function removeFilmCategory(FilmCategory $filmCategory): static
    {
        if ($this->filmCategories->removeElement($filmCategory)) {
            // set the owning side to null (unless already changed)
            if ($filmCategory->getFilm() === $this) {
                $filmCategory->setFilm(null);
            }
        }

        return $this;
    }
}
