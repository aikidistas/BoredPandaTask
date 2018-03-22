<?php

namespace App\Service\Youtube;

use Google_Service_YouTube;
use BadFunctionCallException;
use Google_Service_YouTube_ChannelListResponse;
use App\Exception\YoutubeNotFoundException;

class ChannelService
{
    protected $service;
    protected $channelId = null;

    protected $response = null;

    public function __construct(Google_Service_YouTube $service)
    {
        $this->service = $service;
    }

    public function setChannelId($channelId) : void
    {
        $this->channelId = $channelId;
        $this->response = null;
    }

    /**
     * @throws YoutubeNotFoundException
     * */
    public function getUploadedVideoPlaylistId() : string
    {
        if (is_null($this->channelId)) {
            throw new BadFunctionCallException("You need to setChannelId() before calling getUploadedVideoPlaylistId");
        }

        if (is_null($this->response)) {
            $this->response = $this->executeListChannels();
        }

        if (!is_array($this->response->getItems()) || sizeof($this->response->getItems()) === 0) {
            throw new YoutubeNotFoundException();
        }

        return $this->response->getItems()[0]->getContentDetails()->getRelatedPlaylists()->getUploads();
    }

    protected function executeListChannels() : Google_Service_YouTube_ChannelListResponse
    {
        $part ='contentDetails'; // other: statistics,snippet
        $params = array('id' => $this->channelId);
        return $this->service->channels->listChannels($part, $params);
    }
}