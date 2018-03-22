<?php
/**
 * Created by PhpStorm.
 * User: aikidistas
 * Date: 2018-03-21
 * Time: 5:33 PM
 */

namespace App\Tests\Service\Youtube;

use App\Service\Youtube\ChannelService;
use Google_Service_YouTube;
use BadFunctionCallException;
use Google_Service_YouTube_Resource_Channels;
use Google_Service_YouTube_ChannelListResponse;
use Google_Service_YouTube_Channel;

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
    public function testGetLikeCount_throwsException_WhenVideoIdNotSet()
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
    public function testGetLikeCount_returnsLikeCountFromGoogleService()
    {
        // SET UP
        $resourceChannelsMock = $this->createMock(Google_Service_YouTube_Resource_Channels::class);
        $responseMock = $this->createMock(Google_Service_YouTube_ChannelListResponse::class);
        $responseChannelItemMock = $this->createMock(Google_Service_YouTube_Channel::class);
//        $responseVideoStatisticsMock = $this->createMock(Google_Service_YouTube_VideoStatistics::class);
        $this->youtubeServiceMock->channels = $resourceChannelsMock;

        $resourceChannelsMock->method('listChannels')->willReturn($responseMock);
        $responseMock->method('getItems')->willReturn(array($responseChannelItemMock));
//        $responseChannelItemMock->method('getStatistics')->willReturn($responseVideoStatisticsMock);

        /*
         *  "items": [
  {


   "kind": "youtube#channel",
   "etag": "\"RmznBCICv9YtgWaaa_nWDIH1_GM/OWPMdSRlXQKVMpuGJHLcZMfaauk\"",
   "id": "UC_x5XG1OV2P6uZZ5FSM9Ttw",
   "contentDetails": {
    "relatedPlaylists": {
     "uploads": "UU_x5XG1OV2P6uZZ5FSM9Ttw",
         * */
        // GIVEN
        $expectedPlaylistId = 'PLAYLIST_ID_FOR_TEST';
        //$responseVideoStatisticsMock->method('getLikeCount')->willReturn($expectedLikeCount);
        $this->channelService->setChannelId("VIDEO_ID");

        // WHEN
        $actualPlaylistId = $this->channelService->getUploadedVideoPlaylistId();

        // THEN
        $this->assertEquals($expectedPlaylistId, $actualPlaylistId, "Wrong playlistId returned from ChannelService");
    }
}
