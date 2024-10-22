<?php

namespace App\Entity;

use App\Repository\FavoriteTrackRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FavoriteTrackRepository::class)]
class FavoriteTrack
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $trackId = null;

    #[ORM\Column(length: 255)]
    private ?string $trackName = null;

    #[ORM\Column(length: 255)]
    private ?string $artistName = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picture = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getTrackId(): ?string
    {
        return $this->trackId;
    }

    public function setTrackId(?string $trackId): FavoriteTrack
    {
        $this->trackId = $trackId;
        return $this;
    }

    public function getTrackName(): ?string
    {
        return $this->trackName;
    }

    public function setTrackName(?string $trackName): FavoriteTrack
    {
        $this->trackName = $trackName;
        return $this;
    }

    public function getArtistName(): ?string
    {
        return $this->artistName;
    }

    public function setArtistName(?string $artistName): FavoriteTrack
    {
        $this->artistName = $artistName;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

}