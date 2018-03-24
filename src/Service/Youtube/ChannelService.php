<?php

namespace App\Service\Youtube;

use App\Entity\Channel;
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

    public function getChannelEntity() : Channel
    {
        if (is_null($this->channelId)) {
            throw new BadFunctionCallException("You need to setChannelId() before calling this method");
        }

        $channel = new Channel($this->channelId);
        try {
            $channel->setExternalUploadsPlaylistId(
                $this->getUploadedVideoPlaylistId()
            );
        } catch (YoutubeNotFoundException $e) {
            // just ignore if uploads channel doesn't exist for this channel
        }

        return $channel;
    }

    /**
     * @throws YoutubeNotFoundException
     **/
    public function getUploadedVideoPlaylistId() : string
    {
        if (is_null($this->channelId)) {
            throw new BadFunctionCallException("You need to setChannelId() before calling this method");
        }

        if (!is_array($this->getResponse()->getItems()) || sizeof($this->getResponse()->getItems()) === 0) {
            throw new YoutubeNotFoundException();
        }

        return $this->getResponse()->getItems()[0]->getContentDetails()->getRelatedPlaylists()->getUploads();
    }

    /**
     * @throws YoutubeNotFoundException
     **/
    public function getTitle() : string
    {
        if (is_null($this->channelId)) {
            throw new BadFunctionCallException("You need to setChannelId() before calling this method");
        }

        if (!is_array($this->getResponse()->getItems()) || sizeof($this->getResponse()->getItems()) === 0) {
            throw new YoutubeNotFoundException();
        }

        return $this->getResponse()->getItems()[0]->getSnippet()->getTitle();
    }

    protected function executeListChannels() : Google_Service_YouTube_ChannelListResponse
    {
        $part ='contentDetails,snippet'; // other: statistics,snippet
        $params = array('id' => $this->channelId);
        return $this->service->channels->listChannels($part, $params);
    }

    protected function getResponse(): Google_Service_YouTube_ChannelListResponse
    {
        if (is_null($this->response)) {
            $this->response = $this->executeListChannels();
        }

        return $this->response;
    }
}