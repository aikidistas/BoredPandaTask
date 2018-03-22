<?php
/**
 * Created by PhpStorm.
 * User: aikidistas
 * Date: 2018-03-21
 * Time: 7:45 PM
 */

namespace App\Service\Youtube;

use Google_Service_YouTube;
use BadFunctionCallException;
use Google_Service_YouTube_PlaylistListResponse;
use App\Exception\YoutubeNotFoundException;

class PlaylistService
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

    public function getPlaylistIdArray() : array
    {
        if (is_null($this->channelId)) {
            throw new BadFunctionCallException("You need to setChannelId() before calling getPlaylistIdArray");
        }

        if (is_null($this->response)) {
            $this->response = $this->executeListPlaylists();
        }

        $playlistIdArray = array();

        foreach ($this->response->getItems() as $playlist) {
            $playlistIdArray[] = $playlist->getId();
        }

        return $playlistIdArray;
//        return $this->response->getItems()->getId();
    }

    protected function executeListPlaylists() : Google_Service_YouTube_PlaylistListResponse
    {
        $part ='contentDetails'; // other: statistics,snippet
        $params = array('channelId' => $this->channelId);
        return $this->service->playlists->listPlaylists($part, $params);
    }
}