<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VideoRepository")
 */
class Video
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="string", length=11)
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Channel", inversedBy="uploadedVideos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $channel;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Tag", mappedBy="video", orphanRemoval=true)
     */
    private $tags;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\VersionedLike", mappedBy="video", orphanRemoval=true)
     */
    private $versionedLikes;

    public function __construct(?string $id)
    {
        if (isset($id)) {
            $this->setId($id);
        }
        $this->tags = new ArrayCollection();
        $this->versionedLikes = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getChannel(): ?Channel
    {
        return $this->channel;
    }

    public function setChannel(?Channel $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->setVideo($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
            // set the owning side to null (unless already changed)
            if ($tag->getVideo() === $this) {
                $tag->setVideo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|VersionedLike[]
     */
    public function getVersionedLikes(): Collection
    {
        return $this->versionedLikes;
    }

    public function addVersionedLike(VersionedLike $like): self
    {
        if (!$this->versionedLikes->contains($like)) {
            $this->versionedLikes[] = $like;
            $like->setVideo($this);
        }

        return $this;
    }

    public function removeVersionedLike(VersionedLike $like): self
    {
        if ($this->versionedLikes->contains($like)) {
            $this->versionedLikes->removeElement($like);
            // set the owning side to null (unless already changed)
            if ($like->getVideo() === $this) {
                $like->setVideo(null);
            }
        }

        return $this;
    }
}
