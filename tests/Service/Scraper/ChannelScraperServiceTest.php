<?php

namespace App\Tests\Service\Scraper;

use App\Entity\Channel;
use App\Repository\ChannelRepository;
use App\Service\Scraper\ChannelScraperService;
use App\Service\Scraper\VideoScraperService;
use App\Service\Youtube\ChannelService;
use App\Service\Youtube\PlaylistItemsService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class ChannelScraperServiceTest extends \PHPUnit_Framework_TestCase
{
    /* @var ChannelScraperService */
    protected $channelScraperService;
    /* @var ChannelService*/
    protected $channelServiceMock;
    /* @var ChannelRepository*/
    protected $channelRepositoryMock;
    /* @var PlaylistItemsService*/
    protected $playlistItemsServiceMock;
    /* @var VideoScraperService*/
    protected $videoScraperServiceMock;
    /* @var EntityManagerInterface*/
    protected $entityManager;

    public function setUp()
    {
        $this->channelServiceMock = $this->createMock(ChannelService::class);
        $this->channelRepositoryMock = $this->createMock(ChannelRepository::class);
        $this->playlistItemsServiceMock = $this->createMock(PlaylistItemsService::class);
        $this->videoScraperServiceMock = $this->createMock(VideoScraperService::class);
        $this->entityManager = $this->createMock(EntityManager::class);

        $this->channelScraperService = new ChannelScraperService(
            $this->channelServiceMock,
            $this->channelRepositoryMock,
            $this->playlistItemsServiceMock,
            $this->videoScraperServiceMock,
            $this->entityManager
        );
    }

    /**
     * @throws \App\Exception\YoutubeNotFoundException
     * @throws \Doctrine\ORM\ORMException
     */
    public function testScrapeChannel_foundLocally_returnsEntityFromDatabase()
    {
        // GIVEN
        $expectedChannel = new Channel();
        $this->channelRepositoryMock->method('find')->willReturn($expectedChannel);

        // WHEN
        $actualChannel = $this->channelScraperService->scrapeChannel('CHANNEL_ID');

        // THEN
        $this->assertSame($expectedChannel, $actualChannel);
    }

    /**
     * @throws \App\Exception\YoutubeNotFoundException
     * @throws \Doctrine\ORM\ORMException
     */
    public function testScrapeChannel_notFoundLocally_returnsEntityFromYoutube()
    {
        // GIVEN
        $expectedChannel = new Channel();
        $this->channelRepositoryMock->method('find')->willReturn(null);
        $this->channelServiceMock->method('getChannelEntity')->willReturn($expectedChannel);

        // WHEN
        $actualChannel = $this->channelScraperService->scrapeChannel('CHANNEL_ID');

        // THEN
        $this->assertSame($expectedChannel, $actualChannel);
    }

    /**
     * @throws \App\Exception\YoutubeNotFoundException
     * @throws \Doctrine\ORM\ORMException
     */
    public function testScrapeChannel_notFoundLocally_StoresLocallyEntityFromYoutube()
    {
        // SET UP
        $expectedChannel = new Channel();
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($expectedChannel));
        $this->entityManager->expects($this->once())
            ->method('flush');

        // GIVEN
        $this->channelRepositoryMock->method('find')->willReturn(null);
        $this->channelServiceMock->method('getChannelEntity')->willReturn($expectedChannel);

        // WHEN
        $actualChannel = $this->channelScraperService->scrapeChannel('CHANNEL_ID');

        // THEN
        // Channel downloaded from youtube and stored locally in database
    }

    public function testScrapeChannelPlaylist_downloadsVideo()
    {
        // SET UP
        $expectedPlaylistId = 'UPLOADS_PLAYLIST_ID';
        $channel = new Channel('CHANNEL_ID');
        $video1ID = 'VIDEO_ID_1';
        $video2ID = 'VIDEO_ID_2';

        // GIVEN
        $channel->setUploadsPlaylistId($expectedPlaylistId);
        $this->playlistItemsServiceMock->expects($this->once())
            ->method('setPlaylistId')
            ->with($this->equalTo($expectedPlaylistId));
        $this->playlistItemsServiceMock->method('getVideoIdArray')->willReturn([$video1ID, $video2ID]);
        $this->videoScraperServiceMock
            ->method('scrapeVideo')
            ->withConsecutive(
                [$this->equalTo($video1ID), $channel],
                [$this->equalTo($video2ID), $channel]
            );

        // WHEN
        $this->channelScraperService->scrapeChannelPlaylist($channel);

        // THEN
        // services to download video info will be called
    }

    public function testScrapeChannelPlaylist_persistsDownloadedChanelVideos()
    {
        // GIVEN
        $channel = new Channel('CHANNEL_ID');
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($channel));
        $this->entityManager->expects($this->once())
            ->method('flush');

        // WHEN
        $this->channelScraperService->scrapeChannelPlaylist($channel);

        // THEN
        // changes persisted to database
    }
}
