<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity()]
class Film
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['film:read', 'film:write'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['film:read', 'film:write'])]
    #[Assert\NotBlank(message: "The title cannot be blank.")]
    private $title;

    #[ORM\Column(type: 'text')]
    #[Groups(['film:read', 'film:write'])]
    #[Assert\NotBlank(message: "The description cannot be blank.")]
    private $description;

    #[ORM\Column(type: 'date')]
    #[Groups(['film:read', 'film:write'])]
    private $releaseDate;

    #[ORM\ManyToOne(targetEntity: Genre::class, inversedBy: "films")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Genre $genre = null;
    

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['film:read', 'film:write'])]
    private $imagePath;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'film')]
    private Collection $reviews;

    /**
     * @var Collection<int, Favorites>
     */
    #[ORM\OneToMany(targetEntity: Favorites::class, mappedBy: 'film')]
    private Collection $favorites;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
        $this->favorites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(\DateTimeInterface $releaseDate): self
    {
        $this->releaseDate = $releaseDate;
        return $this;
    }

    public function getGenre(): ?Genre
    {
        return $this->genre;
    }
    
    public function setGenre(?Genre $genre): self
    {
        $this->genre = $genre;
        return $this;
    }
    

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(?string $imagePath): self
    {
        $this->imagePath = $imagePath;
        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setFilm($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getFilm() === $this) {
                $review->setFilm(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Favorites>
     */
    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Favorites $favorite): static
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites->add($favorite);
            $favorite->setFilm($this);
        }

        return $this;
    }

    public function removeFavorite(Favorites $favorite): static
    {
        if ($this->favorites->removeElement($favorite)) {
            // set the owning side to null (unless already changed)
            if ($favorite->getFilm() === $this) {
                $favorite->setFilm(null);
            }
        }

        return $this;
    }
}
