<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChannelRepository")
 */
class Channel
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $externalChannelId;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=34)
     */
    private $externalUploadsPlaylistId;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Video", mappedBy="channelId", orphanRemoval=true)
     */
    private $uploadedVideos;

    public function __construct()
    {
        $this->uploadedVideos = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getExternalChannelId(): ?string
    {
        return $this->externalChannelId;
    }

    public function setExternalChannelId(string $externalChannelId): self
    {
        $this->externalChannelId = $externalChannelId;

        return $this;
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

    public function getExternalUploadsPlaylistId(): ?string
    {
        return $this->externalUploadsPlaylistId;
    }

    public function setExternalUploadsPlaylistId(string $externalUploadsPlaylistId): self
    {
        $this->externalUploadsPlaylistId = $externalUploadsPlaylistId;

        return $this;
    }

    /**
     * @return Collection|Video[]
     */
    public function getUploadedVideos(): Collection
    {
        return $this->uploadedVideos;
    }

    public function addUploadedVideo(Video $uploadedVideo): self
    {
        if (!$this->uploadedVideos->contains($uploadedVideo)) {
            $this->uploadedVideos[] = $uploadedVideo;
            $uploadedVideo->setChannelId($this);
        }

        return $this;
    }

    public function removeUploadedVideo(Video $uploadedVideo): self
    {
        if ($this->uploadedVideos->contains($uploadedVideo)) {
            $this->uploadedVideos->removeElement($uploadedVideo);
            // set the owning side to null (unless already changed)
            if ($uploadedVideo->getChannelId() === $this) {
                $uploadedVideo->setChannelId(null);
            }
        }

        return $this;
    }
}
