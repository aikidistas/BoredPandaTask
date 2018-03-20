<?php

namespace App\Service\Youtube;

use Google_Service_YouTube;
use Google_Service_YouTube_VideoListResponse;
use App\Exception\YoutubeNotFoundException;
use BadFunctionCallException;


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
     * @throws YoutubeNotFoundException
     * @throws BadFunctionCallException
     * */
    public function getTags() : array
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

    protected function executeListVideos() : Google_Service_YouTube_VideoListResponse
    {
        $part ='statistics,snippet'; //, //snippet.tags[]      // contentDetails
        //$params = array('id' => 'Ks-_Mh1QhMc'); //);
        $params = array('id' => $this->videoId); //);
        return $this->service->videos->listVideos($part, $params);
    }
}