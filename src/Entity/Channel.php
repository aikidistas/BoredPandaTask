<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChannelRepository")
 * @codeCoverageIgnore
 */
class Channel
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="string", length=24)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     *
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=34)
     */
    private $uploadsPlaylistId;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Video", mappedBy="channel", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $uploadedVideos;

    public function __construct(?string $id = null)
    {
        $this->uploadedVideos = new ArrayCollection();
        if (isset($id)) {
            $this->id = $id;
        }
    }

    public function getId()
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

    public function getUploadsPlaylistId(): ?string
    {
        return $this->uploadsPlaylistId;
    }

    public function setUploadsPlaylistId(string $uploadsPlaylistId): self
    {
        $this->uploadsPlaylistId = $uploadsPlaylistId;

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
            $uploadedVideo->setChannel($this);
        }

        return $this;
    }

    public function removeUploadedVideo(Video $uploadedVideo): self
    {
        if ($this->uploadedVideos->contains($uploadedVideo)) {
            $this->uploadedVideos->removeElement($uploadedVideo);
            // set the owning side to null (unless already changed)
            if ($uploadedVideo->getChannel() === $this) {
                $uploadedVideo->setChannel(null);
            }
        }

        return $this;
    }
}
