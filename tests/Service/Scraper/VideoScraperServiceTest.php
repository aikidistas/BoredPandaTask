<?php

namespace App\Tests\Service\Scraper;

use App\Entity\Channel;
use App\Entity\Video;
use App\Repository\VideoRepository;
use App\Service\Scraper\VideoScraperService;
use App\Service\Youtube\VideoService;
use Doctrine\ORM\EntityManager;

class VideoScraperServiceTest extends \PHPUnit_Framework_TestCase
{
    /* @var VideoService */
    protected $videoServiceMock;
    /* @var VideoRepository */
    protected $videoRepositoryMock;
    /* @var EntityManagerInterface */
    protected $entityManagerMock;
    /* @var VideoScraperService */
    protected $videoScraperServide;

    public function setUp()
    {
        $this->videoServiceMock = $this->createMock(VideoService::class);
        $this->videoRepositoryMock = $this->createMock(VideoRepository::class);
        $this->entityManagerMock = $this->createMock(EntityManager::class);
        $this->videoScraperServide = new VideoScraperService(
            $this->videoServiceMock,
            $this->videoRepositoryMock,
            $this->entityManagerMock
        );


    }

    public function testScrapeVideo_updateExistingVideo()
    {
        // GIVEN
        $videoId = 'VIDEO_ID';
        $channel = new Channel('CHANNEL_ID');
        $video = new Video($videoId);
        $this->videoRepositoryMock->method('find')->willReturn($video);
        $this->videoServiceMock->expects($this->once())
            ->method('getUpdatedVideoEntity')
            ->willReturn($video);
        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($video));
        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        // WHEN
        $this->videoScraperServide->scrapeVideo($videoId, $channel);

        // THEN
        // video will be taken from database, updates and stored again
    }

    public function testScrapeVideo_scrapeNewVideo()
    {
        // GIVEN
        $this->videoRepositoryMock->method('find')->willReturn(null);

        $videoId = 'VIDEO_ID';
        $channel = new Channel('CHANNEL_ID');
        $video = new Video($videoId);
        $this->videoServiceMock->expects($this->once())
            ->method('getVideoEntity')
            ->willReturn($video);
        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($video));
        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        // WHEN
        $this->videoScraperServide->scrapeVideo($videoId, $channel);

        // THEN
        // video info will be taken from youtube api and stored in database
    }
}
