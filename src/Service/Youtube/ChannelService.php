<?php

namespace App\Service\Youtube;

use Google_Service_YouTube;
use BadFunctionCallException;
use Google_Service_YouTube_ChannelListResponse;

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

    // FIXME: change return of ChannelService from videoIdList to PlaylistId
    public function getVideoIdArray() : array
    {
        if (is_null($this->channelId)) {
            throw new BadFunctionCallException("You need to setChannelId() before calling getVideoIdArray");
        }

        if (is_null($this->response)) {
            $this->response = $this->executeListChannels();
        }

        return array();
    }

    public function getVideoIdListAsText() : string
    {
        return implode(", ", $this->getVideoIdArray());
    }

    protected function executeListChannels() : Google_Service_YouTube_ChannelListResponse
    {
        $part ='contentDetails'; // other: statistics,snippet
        //$params = array('id' => 'UCydKucK3zAWRuHKbB4nJjtw');
        $params = array('id' => $this->channelId);
        return $this->service->channels->listChannels($part, $params);
    }
}