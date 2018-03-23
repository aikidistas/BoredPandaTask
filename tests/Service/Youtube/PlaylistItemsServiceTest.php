<?php

namespace App\Tests\Service\Youtube;

use App\Service\Youtube\PlaylistItemsService;
use App\Exception\YoutubeNotFoundException;
use Google_Service_YouTube;
use BadFunctionCallException;
use Google_Service_YouTube_Resource_Channels;
use Google_Service_YouTube_ChannelListResponse;
use Google_Service_YouTube_Channel;
use Google_Service_YouTube_PlaylistItemContentDetails;
use Google_Service_YouTube_ChannelContentDetailsRelatedPlaylists;
use Google_Service_YouTube_Resource_PlaylistItems;
use Google_Service_YouTube_PlaylistItemListResponse;
use Google_Service_YouTube_PlaylistItem;

class PlaylistItemsServiceTest extends \PHPUnit_Framework_TestCase
{
    /* @var Google_Service_YouTube */
    protected $youtubeServiceMock;
    /* @var PlaylistItemsService */
    protected $playlistItemsService;

    public function setUp()
    {
        $this->youtubeServiceMock = $this->createMock(Google_Service_YouTube::class);
        $this->playlistItemsService = new PlaylistItemsService($this->youtubeServiceMock);
    }

    public function testSetChannelId()
    {
        $this->playlistItemsService->setPlaylistId("PLAYLIST_ID");
    }

    /* @throws YoutubeNotFoundException */
    public function testGetVideoIdArray_throwsException_WhenPlylistIdNotSet()
    {
        // SET UP
        $this->expectException(BadFunctionCallException::class);

        // GIVEN
        // PlaylistId is not set

        // WHEN
        $this->playlistItemsService->getVideoIdArray();

        // THEN
        // exception is thrown
    }

    /* @throws YoutubeNotFoundException */
    public function testGetVideoIdArray_returnsPlaylistItemsIdArray()
    {
        // SET UP
        $resourcePlaylistsItemsMock = $this->createMock(Google_Service_YouTube_Resource_PlaylistItems::class);
        $responseMock = $this->createMock(\Google_Service_YouTube_PlaylistItemListResponse::class);
        $this->youtubeServiceMock->playlistItems = $resourcePlaylistsItemsMock;
        $resourcePlaylistsItemsMock->method('listPlaylistItems')->willReturn($responseMock);
        $this->playlistItemsService->setPlaylistId("PLAYLIST_ID");

        $responsePlaylistItemMock1 = $this->createMock(Google_Service_YouTube_PlaylistItem::class);
        $responsePlaylistItemMock2 = $this->createMock(Google_Service_YouTube_PlaylistItem::class);
        $responseMock->method('getItems')->willReturn(
            array(
                $responsePlaylistItemMock1,
                $responsePlaylistItemMock2
            )
        );

        $contentDetailsMock1 = $this->createMock(Google_Service_YouTube_PlaylistItemContentDetails::class);
        $contentDetailsMock2 = $this->createMock(Google_Service_YouTube_PlaylistItemContentDetails::class);

        $responsePlaylistItemMock1->method('getContentDetails')->willReturn($contentDetailsMock1);
        $responsePlaylistItemMock2->method('getContentDetails')->willReturn($contentDetailsMock2);

        // GIVEN
        $videoId1 = 'VIDEO_ID_1_FOR_TEST';
        $videoId2 = 'VIDEO_ID_2_FOR_TEST';
        $contentDetailsMock1->method('getVideoId')->willReturn($videoId1);
        $contentDetailsMock2->method('getVideoId')->willReturn($videoId2);
        $this->playlistItemsService->setPlaylistId("PLAYLIST_ID");

        // WHEN
        $actualVideoIdArray = $this->playlistItemsService->getVideoIdArray();

        // THEN
        $this->assertEquals(
            array($videoId1, $videoId2),
            $actualVideoIdArray,
            "Wrong VideoId array returned from PlaylistItemService"
        );
    }

    /* @throws YoutubeNotFoundException */
    public function testgetVideoIdListAsText_returnsPlaylistItemsIdArray()
    {
        // SET UP
        $resourcePlaylistsItemsMock = $this->createMock(Google_Service_YouTube_Resource_PlaylistItems::class);
        $responseMock = $this->createMock(\Google_Service_YouTube_PlaylistItemListResponse::class);
        $this->youtubeServiceMock->playlistItems = $resourcePlaylistsItemsMock;
        $resourcePlaylistsItemsMock->method('listPlaylistItems')->willReturn($responseMock);
        $this->playlistItemsService->setPlaylistId("PLAYLIST_ID");

        $responsePlaylistItemMock1 = $this->createMock(Google_Service_YouTube_PlaylistItem::class);
        $responsePlaylistItemMock2 = $this->createMock(Google_Service_YouTube_PlaylistItem::class);
        $responseMock->method('getItems')->willReturn(
            array(
                $responsePlaylistItemMock1,
                $responsePlaylistItemMock2
            )
        );

        $contentDetailsMock1 = $this->createMock(Google_Service_YouTube_PlaylistItemContentDetails::class);
        $contentDetailsMock2 = $this->createMock(Google_Service_YouTube_PlaylistItemContentDetails::class);

        $responsePlaylistItemMock1->method('getContentDetails')->willReturn($contentDetailsMock1);
        $responsePlaylistItemMock2->method('getContentDetails')->willReturn($contentDetailsMock2);

        // GIVEN
        $videoId1 = 'VIDEO_ID_1_FOR_TEST';
        $videoId2 = 'VIDEO_ID_2_FOR_TEST';
        $contentDetailsMock1->method('getVideoId')->willReturn($videoId1);
        $contentDetailsMock2->method('getVideoId')->willReturn($videoId2);
        $this->playlistItemsService->setPlaylistId("PLAYLIST_ID");

        // WHEN
        $actualVideoIdArray = $this->playlistItemsService->getVideoIdListAsText();

        // THEN
        $expectedResult = 'VIDEO_ID_1_FOR_TEST, VIDEO_ID_2_FOR_TEST';
        $this->assertEquals(
            $expectedResult,
            $actualVideoIdArray,
            "Wrong VideoId list returned from PlaylistItemService"
        );
    }
}
