<?php

namespace App\Tests\Service\Youtube;

use App\Service\Youtube\PlaylistService;
use App\Exception\YoutubeNotFoundException;
use Google_Service_YouTube;
use BadFunctionCallException;
use Google_Service_YouTube_Resource_Channels;
use Google_Service_YouTube_ChannelListResponse;
use Google_Service_YouTube_Channel;
use Google_Service_YouTube_ChannelContentDetails;
use Google_Service_YouTube_ChannelContentDetailsRelatedPlaylists;
use Google_Service_YouTube_Resource_Playlists;
use Google_Service_YouTube_PlaylistListResponse;
use Google_Service_YouTube_Playlist;

class PlaylistServiceTest extends \PHPUnit_Framework_TestCase
{
    /* @var Google_Service_YouTube */
    protected $youtubeServiceMock;
    /* @var PlaylistService */
    protected $playlistService;

    public function setUp()
    {
        $this->youtubeServiceMock = $this->createMock(Google_Service_YouTube::class);
        $this->playlistService = new PlaylistService($this->youtubeServiceMock);
    }

    public function testSetChannelId()
    {
        $this->playlistService->setChannelId("CHANNEL_ID");
    }

    /* @throws YoutubeNotFoundException */
    public function testGetPlaylistIdArray_throwsException_WhenChannelIdNotSet()
    {
        // SET UP
        $this->expectException(BadFunctionCallException::class);

        // GIVEN
        // ChannelId is not set

        // WHEN
        $this->playlistService->getPlaylistIdArray();

        // THEN
        // exception is thrown
    }

    /* @throws YoutubeNotFoundException */
    public function testGetPlaylistIdArray_returnsPlaylistIdArray()
    {
        // SET UP
        $resourcePlaylistsMock = $this->createMock(Google_Service_YouTube_Resource_Playlists::class);
        $responseMock = $this->createMock(Google_Service_YouTube_PlaylistListResponse::class);
        $this->youtubeServiceMock->playlists = $resourcePlaylistsMock;
        $resourcePlaylistsMock->method('listPlaylists')->willReturn($responseMock);
        $this->playlistService->setChannelId("CHANNEL_ID");

        $responsePlaylistItemMock1 = $this->createMock(Google_Service_YouTube_Playlist::class);
        $responsePlaylistItemMock2 = $this->createMock(Google_Service_YouTube_Playlist::class);
        $responseMock->method('getItems')->willReturn(
            array(
                $responsePlaylistItemMock1,
                $responsePlaylistItemMock2
            )
        );

        // GIVEN
        $playlistId1 = 'PLAYLIST_ID_1_FOR_TEST';
        $playlistId2 = 'PLAYLIST_ID_2_FOR_TEST';
        $responsePlaylistItemMock1->method('getId')->willReturn($playlistId1);
        $responsePlaylistItemMock2->method('getId')->willReturn($playlistId2);
        $this->playlistService->setChannelId("VIDEO_ID");

        // WHEN
        $actualPlaylistIdArray = $this->playlistService->getPlaylistIdArray();

        // THEN
        $this->assertEquals(
            array($playlistId1, $playlistId2),
            $actualPlaylistIdArray,
            "Wrong playlistId array returned from PlaylistService"
        );
    }
}
