<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VideoRepository")
 */
class Video
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=11)
     */
    private $externalVideoId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Channel", inversedBy="uploadedVideos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $channelId;

    public function getId()
    {
        return $this->id;
    }

    public function getExternalVideoId(): ?string
    {
        return $this->externalVideoId;
    }

    public function setExternalVideoId(string $externalVideoId): self
    {
        $this->externalVideoId = $externalVideoId;

        return $this;
    }

    public function getChannelId(): ?Channel
    {
        return $this->channelId;
    }

    public function setChannelId(?Channel $channelId): self
    {
        $this->channelId = $channelId;

        return $this;
    }
}
