<?php
/**
 * Created by PhpStorm.
 * User: aikidistas
 * Date: 2018-03-21
 * Time: 2:18 AM
 */

namespace App\Service\Youtube;

use Google_Service_YouTube;
use Google_Service_Youtube_PlaylistItem;
use App\Exception\BadFunctionCallException;
use App\Exception\YoutubeNotFoundException;

class PlaylistItemsService
{
    protected $service;
    protected $playlistId = null;

    protected $response = null;

    public function __construct(Google_Service_YouTube $service)
    {
        $this->service = $service;
    }

    public function setPlaylistId($playlistId) : void
    {
        $this->playlistId = $playlistId;
        $this->response = null;
    }

    public function getVideoIdArray() : array
    {
        if (is_null($this->playlistId)) {
            throw new BadFunctionCallException("You need to setChannelId() before calling getUploadedVideoPlaylistId");
        }

        if (is_null($this->response)) {
            $this->response = $this->executeListPlaylistItems();
        }

//        var_dump($this->response);
        $videoIdArray = array();
        foreach ($this->response->getItems() as $playlistItem)
        {
//            var_dump($playlistItem);
            $videoIdArray[] = $playlistItem->getContentDetails()->getVideoId();
        }
        return $videoIdArray;
        // $this->response->getItems()[0]->getContentDetails()->getVideoId();
    }

    public function getVideoIdListAsText() : string
    {
        return implode(", ", $this->getVideoIdArray());
    }

    protected function executeListPlaylistItems()
    {
        $part ='contentDetails'; // other: snippet
        $params = array('playlistId' => $this->playlistId);
//        $maxResults = 50;
        return $this->service->playlistItems->listPlaylistItems(
            $part,
            $params
        );
    }
}