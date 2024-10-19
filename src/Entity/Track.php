<?php

namespace App\Entity;

class Track
{
    private ?int $discNumber;
    private ?int $durationMs;
    private ?bool $explicit;
    private ?string $isrc;
    private ?string $spotifyUrl;
    private ?string $href;
    private ?string $id;
    private ?bool $isLocal;
    private ?string $name;
    private ?int $popularity;
    private ?string $previewUrl;
    private ?int $trackNumber;
    private ?string $type;
    private ?string $uri;
    private ?string $pictureLink;
    private ?string $artist;

    public function __construct(
    ) {
        $this->discNumber = null;
        $this->durationMs = null;
        $this->explicit = null;
        $this->isrc = null;
        $this->spotifyUrl = null;
        $this->href = null;
        $this->id = null;
        $this->isLocal = null;
        $this->name = null;
        $this->popularity = null;
        $this->previewUrl = null;
        $this->trackNumber = null;
        $this->type = null;
        $this->uri = null;
        $this->pictureLink = null;
    }

    // Getters for all properties
    public function getDiscNumber(): int
    {
        return $this->discNumber;
    }

    public function getDurationMs(): int
    {
        return $this->durationMs;
    }

    public function isExplicit(): bool
    {
        return $this->explicit;
    }

    public function getIsrc(): string
    {
        return $this->isrc;
    }

    public function getSpotifyUrl(): string
    {
        return $this->spotifyUrl;
    }

    public function getHref(): string
    {
        return $this->href;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function isLocal(): bool
    {
        return $this->isLocal;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPopularity(): int
    {
        return $this->popularity;
    }

    public function getPreviewUrl(): ?string
    {
        return $this->previewUrl;
    }

    public function getTrackNumber(): int
    {
        return $this->trackNumber;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getPictureLink(): string
    {
        return $this->pictureLink;
    }

    public function getArtist(): ?string
    {
        return $this->artist;
    }

    //All setters
    public function setDiscNumber(int $discNumber): void
    {
        $this->discNumber = $discNumber;
    }

    public function setDurationMs(int $durationMs): void
    {
        $this->durationMs = $durationMs;
    }

    public function setExplicit(bool $explicit): void
    {
        $this->explicit = $explicit;
    }

    public function setIsrc(string $isrc): void
    {
        $this->isrc = $isrc;
    }

    public function setSpotifyUrl(string $spotifyUrl): void
    {
        $this->spotifyUrl = $spotifyUrl;
    }

    public function setHref(string $href): void
    {
        $this->href = $href;
    }

    public function setId(string $id): Track
    {
        $this->id = $id;
        return $this;
    }

    public function setIsLocal(bool $isLocal): void
    {
        $this->isLocal = $isLocal;
    }

    public function setName(string $name): Track
    {
        $this->name = $name;
        return $this;
    }

    public function setPopularity(int $popularity): void
    {
        $this->popularity = $popularity;
    }

    public function setPreviewUrl(?string $previewUrl): void
    {
        $this->previewUrl = $previewUrl;
    }

    public function setTrackNumber(int $trackNumber): void
    {
        $this->trackNumber = $trackNumber;
    }

    public function setType(string $type): Track
    {
        $this->type = $type;
        return $this;
    }

    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }

    public function setPictureLink(?string $pictureLink): Track
    {
        $this->pictureLink = $pictureLink;
        return $this;
    }

    public function setArtist(?string $artist): Track
    {
        $this->artist = $artist;
        return $this;
    }

}
