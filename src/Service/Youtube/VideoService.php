<?php

namespace App\Service\Youtube;

use App\Entity\Tag;
use App\Entity\VersionedLike;
use App\Entity\VersionedView;
use App\Entity\Video;
use Google_Service_YouTube;
use Google_Service_YouTube_VideoListResponse;
use App\Exception\YoutubeNotFoundException;
use BadFunctionCallException;
use DateTime;


class VideoService
{
    protected $service;
    protected $videoId = null;

    protected $response = null;

    public function __construct(Google_Service_YouTube $service)
    {
        $this->service = $service;
    }

    public function setVideoId($videoId)
    {
        $this->videoId = $videoId;
        $this->response = null;
    }

    /**
     * @return Video
     * @throws YoutubeNotFoundException
     */
    public function getVideoEntity() : Video
    {
        if (is_null($this->videoId)) {
            throw new BadFunctionCallException("You need to setVideoId() before calling this method");
        }

        $video = new Video($this->videoId);

        $tags = $this->getTags();
        foreach ($tags as $tagText) {
            $tag = new Tag();
            $tag->setText($tagText);
            $video->addTag($tag);
        }

        return $this->getUpdatedVideoEntity($video);
    }

    /**
     * @return Video
     * @throws YoutubeNotFoundException
     */
    public function getUpdatedVideoEntity(Video $video) : Video
    {
        $this->setVideoId($video->getId());

        $video->setTitle($this->getTitle());

        $like = new VersionedLike();
        $like->setDateTime(new DateTime());
        $like->setAmount(
            $this->getLikeCount()
        );
        $video->addVersionedLike($like);

        $view = new VersionedView();
        $view->setDateTime(new DateTime());
        $view->setAmount(
            $this->getViewCount()
        );
        $video->addVersionedView($view);


        return $video;
    }

    /**
     * @throws YoutubeNotFoundException
     * @throws BadFunctionCallException
     * */
    public function getTags() : array
    {
        if (is_null($this->videoId)) {
            throw new BadFunctionCallException("You need to setVideoId() before calling method");
        }

        if (is_null($this->response)) {
            $this->response = $this->executeListVideos();
        }

        if (!is_array($this->response->getItems()) || sizeof($this->response->getItems()) === 0) {
            throw new YoutubeNotFoundException();
        }

        $tags = $this->response->getItems()[0]->getSnippet()->getTags();

        return $tags;
    }

    /**
     * @throws YoutubeNotFoundException
     * @throws BadFunctionCallException
     * */
    public function getTagsInline() : string
    {
        return implode(", ", $this->getTags());
    }

    /**
     * @throws YoutubeNotFoundException
     * @throws BadFunctionCallException
     * */
    public function getTitle() : string
    {
        if (is_null($this->videoId)) {
            throw new BadFunctionCallException("You need to setVideoId() before calling method");
        }

        if (is_null($this->response)) {
            $this->response = $this->executeListVideos();
        }

        if (!is_array($this->response->getItems()) || sizeof($this->response->getItems()) === 0) {
            throw new YoutubeNotFoundException();
        }

        $title = $this->response->getItems()[0]->getSnippet()->getTitle();

        return $title;
    }

    /**
     * @throws YoutubeNotFoundException
     * @throws BadFunctionCallException
     * */
    public function getLikeCount() : int
    {
        if (is_null($this->videoId)) {
            throw new BadFunctionCallException("You need to setVideoId() before calling getTags");
        }

        if (is_null($this->response)) {
            $this->response = $this->executeListVideos();
        }

        if (!is_array($this->response->getItems()) || sizeof($this->response->getItems()) === 0)
        {
            throw new YoutubeNotFoundException();
        }

        $likeCount = $this->response->getItems()[0]->getStatistics()->getLikeCount();

        return $likeCount;
    }

    /**
     * @throws YoutubeNotFoundException
     * @throws BadFunctionCallException
     * */
    public function getViewCount() : int
    {
        if (is_null($this->videoId)) {
            throw new BadFunctionCallException("You need to setVideoId() before calling getTags");
        }

        if (is_null($this->response)) {
            $this->response = $this->executeListVideos();
        }

        if (!is_array($this->response->getItems()) || sizeof($this->response->getItems()) === 0)
        {
            throw new YoutubeNotFoundException();
        }

        $viewCount = $this->response->getItems()[0]->getStatistics()->getViewCount();

        return $viewCount;
    }

    protected function executeListVideos() : Google_Service_YouTube_VideoListResponse
    {
        $part ='statistics,snippet'; // other: contentDetails
        $params = array('id' => $this->videoId);
        return $this->service->videos->listVideos($part, $params);
    }
}