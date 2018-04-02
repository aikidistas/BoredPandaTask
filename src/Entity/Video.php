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
     * @ORM\OneToMany(targetEntity="App\Entity\Tag", mappedBy="video", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $tags;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\VersionedLike", mappedBy="video", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $versionedLikes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\VersionedView", mappedBy="video", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $versionedViews;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $title;

    /**
     * @ORM\Column(type="integer")
     */
    private $firstHourViews;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $performance;

    public function __construct(string $id = null)
    {
        if (isset($id)) {
            $this->id = $id;
        }
        $this->tags = new ArrayCollection();
        $this->versionedLikes = new ArrayCollection();
        $this->versionedViews = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
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

    /**
     * @return Collection|VersionedView[]
     */
    public function getVersionedViews(): Collection
    {
        return $this->versionedViews;
    }

    public function addVersionedView(VersionedView $view): self
    {
        if (!$this->versionedViews->contains($view)) {
            $this->versionedViews[] = $view;
            $view->setVideo($this);
        }

        /* @var $firstView VersionedView*/
        $firstView = $this->versionedViews->first();
        /* @var $lastView VersionedView*/
        $lastView = $view;
        $timeDiff = $firstView->getDateTime()->diff($lastView->getDateTime());
        if ($timeDiff->y === 0 and $timeDiff->m === 0 and $timeDiff->d === 0 and
            ($timeDiff->h < 1 or ($timeDiff->h = 1 and $timeDiff->m <= 1)))
        {
            $firstHourViewsAmount = $lastView->getAmount() - $firstView->getAmount();
            $this->setFirstHourViews($firstHourViewsAmount);
        }

        return $this;
    }

    public function removeVersionedView(VersionedView $view): self
    {
        if ($this->versionedViews->contains($view)) {
            $this->versionedViews->removeElement($view);
            // set the owning side to null (unless already changed)
            if ($view->getVideo() === $this) {
                $view->setVideo(null);
            }
        }

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

    public function getFirstHourViews(): ?int
    {
        return $this->firstHourViews;
    }

    private function setFirstHourViews(int $firstHourViews): self
    {
        $this->firstHourViews = $firstHourViews;

        return $this;
    }

    public function getPerformance(): ?float
    {
        return $this->performance;
    }

    public function setPerformance(?float $performance): self
    {
        $this->performance = $performance;

        return $this;
    }
}
