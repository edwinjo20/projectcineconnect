<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity()]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['review:read', 'review:write'])]
    private $id;

    #[ORM\Column(type: 'text')]
    #[Groups(['review:read', 'review:write'])]
    #[Assert\NotBlank(message: 'Please enter your review.')]
    private $content;

    #[ORM\Column(type: 'integer')]
    #[Groups(['review:read', 'review:write'])]
    #[Assert\Range(
        min: 1,
        max: 10,
        notInRangeMessage: 'Rating must be between {{ min }} and {{ max }}.'
    )]
    private $ratingGiven;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['review:read', 'review:write'])]
    private $publicationDate;

    #[ORM\ManyToOne(targetEntity: Film::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['review:read', 'review:write'])]
    private $film;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['review:read', 'review:write'])]
    private $user;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'review')]
    private Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->publicationDate = new \DateTime(); // Automatically set the publication date when created
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getRatingGiven(): ?int
    {
        return $this->ratingGiven;
    }

    public function setRatingGiven(int $ratingGiven): self
    {
        $this->ratingGiven = $ratingGiven;
        return $this;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(\DateTimeInterface $publicationDate): self
    {
        $this->publicationDate = $publicationDate;
        return $this;
    }

    public function getFilm(): ?Film
    {
        return $this->film;
    }

    public function setFilm(?Film $film): self
    {
        $this->film = $film;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
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
            $comment->setReview($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            if ($comment->getReview() === $this) {
                $comment->setReview(null);
            }
        }

        return $this;
    }
}
