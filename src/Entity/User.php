<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user'])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['comment', 'user'])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(['user'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $comments;

    /**
     * @var Collection<int, FilmVote>
     */
    #[ORM\OneToMany(targetEntity: FilmVote::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $upvoteFilm;

    /**
     * @var Collection<int, CommentVote>
     */
    #[ORM\OneToMany(targetEntity: CommentVote::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $upvoteComment;

    #[ORM\Column(length: 255)]
    #[Groups(['comment', 'user'])]
    private ?string $userName = null;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->upvoteFilm = new ArrayCollection();
        $this->upvoteComment = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        // $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FilmVote>
     */
    public function getUpvoteFilm(): Collection
    {
        return $this->upvoteFilm;
    }

    public function addUpvoteFilm(FilmVote $upvoteFilm): static
    {
        if (!$this->upvoteFilm->contains($upvoteFilm)) {
            $this->upvoteFilm->add($upvoteFilm);
            $upvoteFilm->setUser($this);
        }

        return $this;
    }

    public function removeUpvoteFilm(FilmVote $upvoteFilm): static
    {
        if ($this->upvoteFilm->removeElement($upvoteFilm)) {
            // set the owning side to null (unless already changed)
            if ($upvoteFilm->getUser() === $this) {
                $upvoteFilm->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CommentVote>
     */
    public function getUpvoteComment(): Collection
    {
        return $this->upvoteComment;
    }

    public function addUpvoteComment(CommentVote $upvoteComment): static
    {
        if (!$this->upvoteComment->contains($upvoteComment)) {
            $this->upvoteComment->add($upvoteComment);
            $upvoteComment->setUser($this);
        }

        return $this;
    }

    public function removeUpvoteComment(CommentVote $upvoteComment): static
    {
        if ($this->upvoteComment->removeElement($upvoteComment)) {
            // set the owning side to null (unless already changed)
            if ($upvoteComment->getUser() === $this) {
                $upvoteComment->setUser(null);
            }
        }

        return $this;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): static
    {
        $this->userName = $userName;

        return $this;
    }
}
