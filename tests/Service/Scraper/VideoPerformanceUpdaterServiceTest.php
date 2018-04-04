<?php

namespace App\Tests\Service\Scraper;

use App\Entity\Channel;
use App\Entity\Video;
use App\Repository\ChannelRepository;
use App\Repository\VideoRepository;
use App\Service\Scraper\VideoPerformanceUpdaterService;
use Doctrine\ORM\EntityManagerInterface;

class VideoPerformanceUpdaterServiceTest extends \PHPUnit_Framework_TestCase
{
    /* @var VideoPerformanceUpdaterService */
    protected $videoPerformanceUpdaterService;
    /* @var EntityManagerInterface */
    protected $entityManagerMock;
    /* @var ChannelRepository */
    protected $channelRepositoryMock;
    /* @var VideoRepository */
    protected $videoRepositoryMock;


    public function setUp()
    {
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->channelRepositoryMock = $this->createMock(ChannelRepository::class);
        $this->videoRepositoryMock = $this->createMock(VideoRepository::class);

        $this->videoPerformanceUpdaterService = new VideoPerformanceUpdaterService(
            $this->entityManagerMock,
            $this->channelRepositoryMock,
            $this->videoRepositoryMock
        );
    }

    public function testUpdateChannel_willNotUpdateChannelPerformance_WhenNoViewsYet()
    {
        //GIVEN
        $channelId = 'CHANNEL_ID';
        $median = 0;
        $channel = new Channel($channelId);
        $this->videoRepositoryMock->expects($this->once())
            ->method('selectChannelFirstHourViewsMedian')
            ->with($this->equalTo($channelId))
            ->willReturn($median);

        $this->entityManagerMock->expects($this->never())
            ->method('persist')
            ->with($this->equalTo($channel));


        // WHEN
        $this->videoPerformanceUpdaterService->updateChannel($channel);

        // THEN
        // channel will not be updated when median is 0. This means no channel views yet. Can't calculate performance
    }

    public function testUpdateChannel_willPersistUpdatedChannel_WhenChannelViewsMedianExists()
    {
        // SET UP
        $channelId = 'CHANNEL_ID';
        $channel = new Channel($channelId);
        $median = 11;

        //GIVEN
        $this->videoRepositoryMock->expects($this->once())
            ->method('selectChannelFirstHourViewsMedian')
            ->with($this->equalTo($channelId))
            ->willReturn($median);

        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($channel));
        $this->entityManagerMock->expects($this->once())
            ->method('flush');


        // WHEN
        $this->videoPerformanceUpdaterService->updateChannel($channel);

        // THEN
        // updated channel will be stored using persist, flush methods in entityManager
    }

    public function testUpdateChannel_willCalculateVideosPerformance_WhenChannelViewsMedianExists()
    {
        //GIVEN
        $channelId = 'CHANNEL_ID';
        $median = 10;
        $channel = new Channel($channelId);

        $firstHourViews1 = 5;
        $video1 = new Video();
        $video1->setFirstHourViews($firstHourViews1);
        $channel->addUploadedVideo($video1);

        $firstHourViews2 = 15;
        $video2 = new Video();
        $video2->setFirstHourViews($firstHourViews2);
        $channel->addUploadedVideo($video2);

        $this->videoRepositoryMock->expects($this->once())
            ->method('selectChannelFirstHourViewsMedian')
            ->with($this->equalTo($channelId))
            ->willReturn($median);

        // WHEN
        $this->videoPerformanceUpdaterService->updateChannel($channel);

        // THEN
        $expectedPerformance1 = $firstHourViews1 / $median;
        $expectedPerformance2 = $firstHourViews2 / $median;
        $this->assertEquals($expectedPerformance1, $video1->getPerformance());
        $this->assertEquals($expectedPerformance2, $video2->getPerformance());

    }

    public function testUpdateAllChannels()
    {
        // SET UP
        $videoPerformanceUpdaterServiceMock = $this->getMockBuilder(VideoPerformanceUpdaterService::class)
            ->setConstructorArgs([
                $this->entityManagerMock,
                $this->channelRepositoryMock,
                $this->videoRepositoryMock
            ])
            ->setMethods(['updateChannel'])
            ->getMock();
        $channel1 = new Channel('CHANNEL_ID_1');
        $channel2 = new Channel('CHANNEL_ID_2');
        $this->channelRepositoryMock->method('findAll')->willReturn([$channel1, $channel2]);

        // GIVEN
        $videoPerformanceUpdaterServiceMock->expects($this->exactly(2))
            ->method('updateChannel')
            ->withConsecutive(
                [$channel1],
                [$channel2]
            );

        // WHEN
        $videoPerformanceUpdaterServiceMock->updateAllChannels();

        // THEN
        //updateChannel is called for all channels returned by channelRepository->findAll()
    }
}
