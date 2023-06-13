<?php

namespace App\Entity;

use App\Repository\MovieRepository;
use App\Validator\Constraints\Movie\Poster;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;

#[ORM\Entity(repositoryClass: MovieRepository::class)]
class Movie
{
    public const SLUG_FORMAT = '\d{4}-\w+(-\w+)*';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[NotNull]
    #[Regex(pattern: '#'.self::SLUG_FORMAT.'#')]
    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[NotNull]
    #[Length(min: 3)]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[NotNull]
    #[Poster]
    #[ORM\Column(length: 255)]
    private ?string $poster = null;

    #[NotNull]
    #[LessThanOrEqual('+3 years')]
    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private ?\DateTimeImmutable $releasedAt = null;

    #[NotNull]
    #[Length(min: 20)]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $plot = null;

    #[NotNull]
    #[Count(min: 1)]
    #[ORM\ManyToMany(targetEntity: Genre::class, inversedBy: 'movies')]
    private Collection $genres;

    public function __construct()
    {
        $this->genres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
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

    public function getPoster(): ?string
    {
        return $this->poster;
    }

    public function setPoster(string $poster): static
    {
        $this->poster = $poster;

        return $this;
    }

    public function getReleasedAt(): ?\DateTimeImmutable
    {
        return $this->releasedAt;
    }

    public function setReleasedAt(\DateTimeImmutable $releasedAt): static
    {
        $this->releasedAt = $releasedAt;

        return $this;
    }

    public function getPlot(): ?string
    {
        return $this->plot;
    }

    public function setPlot(string $plot): static
    {
        $this->plot = $plot;

        return $this;
    }

    /**
     * @return Collection<int, Genre>
     */
    public function getGenres(): Collection
    {
        return $this->genres;
    }

    public function addGenre(Genre $genre): static
    {
        if (!$this->genres->contains($genre)) {
            $this->genres->add($genre);
        }

        return $this;
    }

    public function removeGenre(Genre $genre): static
    {
        $this->genres->removeElement($genre);

        return $this;
    }
}
