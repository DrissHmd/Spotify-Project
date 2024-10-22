<?php

namespace App\Entity;

use App\Repository\FavoriteArtistRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FavoriteArtistRepository::class)]
class FavoriteArtist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $followers = null;

    #[ORM\Column]
    private ?string $picture = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $genres = [];

    #[ORM\Column(length: 255)]
    private ?string $artistId = null;

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

    public function getFollowers(): ?int
    {
        return $this->followers;
    }

    public function setFollowers(int $followers): static
    {
        $this->followers = $followers;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): void
    {
        $this->picture = $picture;
    }

    public function getGenres(): array
    {
        return $this->genres;
    }

    public function setGenres(array $genres): static
    {
        $this->genres = $genres;

        return $this;
    }

    public function getArtistId(): ?string
    {
        return $this->artistId;
    }

    public function setArtistId(string $artistId): static
    {
        $this->artistId = $artistId;

        return $this;
    }
}
