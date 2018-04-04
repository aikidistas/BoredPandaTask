<?php

namespace App\Tests\Service\Youtube;

use App\Service\Youtube\ChannelService;
use App\Exception\YoutubeNotFoundException;
use Google_Service_YouTube;
use BadFunctionCallException;
use Google_Service_YouTube_Resource_Channels;
use Google_Service_YouTube_ChannelListResponse;
use Google_Service_YouTube_Channel;
use Google_Service_YouTube_ChannelContentDetails;
use Google_Service_YouTube_ChannelContentDetailsRelatedPlaylists;
use Google_Service_YouTube_ChannelSnippet;

class ChannelServiceTest extends \PHPUnit_Framework_TestCase
{
    /* @var Google_Service_YouTube */
    protected $youtubeServiceMock;
    /* @var ChannelService */
    protected $channelService;

    public function setUp()
    {
        $this->youtubeServiceMock = $this->createMock(Google_Service_YouTube::class);
        $this->channelService = new ChannelService($this->youtubeServiceMock);
    }

    public function testSetChannelId()
    {
        $this->channelService->setChannelId("CHANNEL_ID");
    }

    /* @throws YoutubeNotFoundException */
    public function testGetUploadedVideoPlaylistId_throwsException_WhenChannelIdNotSet()
    {
        // SET UP
        $this->expectException(BadFunctionCallException::class);

        // GIVEN
        // ChannelId is not set

        // WHEN
        $this->channelService->getUploadedVideoPlaylistId();

        // THEN
        // exception is thrown
    }

    /* @throws YoutubeNotFoundException */
    public function testGetUploadedVideoPlaylistId_returnsPlaylistId()
    {
        // SET UP
        $resourceChannelsMock = $this->createMock(Google_Service_YouTube_Resource_Channels::class);
        $this->youtubeServiceMock->channels = $resourceChannelsMock;
        $responseMock = $this->createMock(Google_Service_YouTube_ChannelListResponse::class);
        $resourceChannelsMock->method('listChannels')->willReturn($responseMock);

        $responseChannelItemMock = $this->createMock(Google_Service_YouTube_Channel::class);
        $responseMock->method('getItems')->willReturn(array($responseChannelItemMock));

        $responseChannelDetailsMock = $this->createMock(Google_Service_YouTube_ChannelContentDetails::class);
        $responseChannelItemMock->method('getContentDetails')->willReturn($responseChannelDetailsMock);

        $responseRelatedPlaylistsMock = $this->createMock(Google_Service_YouTube_ChannelContentDetailsRelatedPlaylists::class);
        $responseChannelDetailsMock->method('getRelatedPlaylists')->willReturn($responseRelatedPlaylistsMock);


        // GIVEN
        $expectedPlaylistId = 'UPLOADS_PLAYLIST_ID_FOR_TEST';
        $responseRelatedPlaylistsMock->method('getUploads')->willReturn($expectedPlaylistId);
        //$responseVideoStatisticsMock->method('getLikeCount')->willReturn($expectedLikeCount);
        $this->channelService->setChannelId("VIDEO_ID");

        // WHEN
        $actualPlaylistId = $this->channelService->getUploadedVideoPlaylistId();

        // THEN
        $this->assertEquals($expectedPlaylistId, $actualPlaylistId, "Wrong playlistId returned from ChannelService");
    }

    /* @throws YoutubeNotFoundException */
    public function testGetUploadedVideoPlaylistId_returnsZeroItemsFoundFromGoogleService()
    {
        // SET UP
        $resourceChannelsMock = $this->createMock(Google_Service_YouTube_Resource_Channels::class);
        $responseMock = $this->createMock(Google_Service_YouTube_ChannelListResponse::class);
        $this->youtubeServiceMock->channels = $resourceChannelsMock;
        $resourceChannelsMock->method('listChannels')->willReturn($responseMock);
        $this->channelService->setChannelId("CHANNEL_ID");

        $this->expectException(YoutubeNotFoundException::class);

        // GIVEN
        $responseMock->method('getItems')->willReturn(array());

        // WHEN
        $this->channelService->getUploadedVideoPlaylistId();

        // THEN
        // not found exception is thrown
    }

    /* @throws YoutubeNotFoundException */
    public function testGetTitle_returnsTitle()
    {
        // SET UP
        $expectedChannelTitle = 'CHANNEL_TITLE';
        $resourceChannelsMock = $this->createMock(Google_Service_YouTube_Resource_Channels::class);
        $this->youtubeServiceMock->channels = $resourceChannelsMock;
        $responseMock = $this->createMock(Google_Service_YouTube_ChannelListResponse::class);
        $resourceChannelsMock->method('listChannels')->willReturn($responseMock);

        $responseChannelItemMock = $this->createMock(Google_Service_YouTube_Channel::class);
        $responseMock->method('getItems')->willReturn(array($responseChannelItemMock));

        $responseChannelSnippetMock = $this->createMock(Google_Service_YouTube_ChannelSnippet::class);
        $responseChannelItemMock->method('getSnippet')->willReturn($responseChannelSnippetMock);

        $responseChannelSnippetMock->method('getTitle')->willReturn($expectedChannelTitle);


        // GIVEN
        $this->channelService->setChannelId("CHANNEL_ID");

        // WHEN
        $actualTitle = $this->channelService->getTitle();

        // THEN
        $this->assertEquals($expectedChannelTitle, $actualTitle, "Wrong title returned from ChannelService");
    }

    /* @throws YoutubeNotFoundException */
    public function testGetTitle_throwsException_WhenChannelIdNotSet()
    {
        // SET UP
        $this->expectException(BadFunctionCallException::class);

        // GIVEN
        // ChannelId is not set

        // WHEN
        $this->channelService->getTitle();

        // THEN
        // exception is thrown
    }

    /* @throws YoutubeNotFoundException */
    public function testGetTitle_returnsZeroItemsFoundFromGoogleService()
    {
        // SET UP
        $resourceChannelsMock = $this->createMock(Google_Service_YouTube_Resource_Channels::class);
        $responseMock = $this->createMock(Google_Service_YouTube_ChannelListResponse::class);
        $this->youtubeServiceMock->channels = $resourceChannelsMock;
        $resourceChannelsMock->method('listChannels')->willReturn($responseMock);
        $this->channelService->setChannelId("CHANNEL_ID");

        $this->expectException(YoutubeNotFoundException::class);

        // GIVEN
        $responseMock->method('getItems')->willReturn(array());

        // WHEN
        $this->channelService->getTitle();

        // THEN
        // not found exception is thrown
    }


    /* @throws YoutubeNotFoundException */
    public function testGetChannelEntity_throwsException_WhenChannelIdNotSet()
    {
        // SET UP
        $this->expectException(BadFunctionCallException::class);

        // GIVEN
        // ChannelId is not set

        // WHEN
        $this->channelService->getChannelEntity();

        // THEN
        // exception is thrown
    }

    public function testGetChannelEntity()
    {
        // SET UP
        $channelServiceMock = $this->getMockBuilder(ChannelService::class)
            ->setConstructorArgs([$this->youtubeServiceMock])
            ->setMethods(['getUploadedVideoPlaylistId', 'getTitle'])
            ->getMock();
        $channelServiceMock->setChannelId('CHANNEL_ID');

        // GIVEN
        $expectedPlaylistId = 'PLAYLIST_ID';
        $expectedChannelTitle = 'CHANNEL_TITLE';
        $channelServiceMock->method('getUploadedVideoPlaylistId')->willReturn($expectedPlaylistId);
        $channelServiceMock->method('getTitle')->willReturn($expectedChannelTitle);

        // WHEN
        $channel = $channelServiceMock->getChannelEntity();

        // THEN
        $this->assertEquals($expectedPlaylistId, $channel->getUploadsPlaylistId());
        $this->assertEquals($expectedChannelTitle, $channel->getTitle());
    }
}
