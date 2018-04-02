<?php


namespace App\Service\Scraper;


use App\Entity\Channel;
use App\Repository\ChannelRepository;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;

class VideoPerformanceUpdaterService
{
    /*
     * @var EntityManagerIngerface
     */
    protected $entityManager;

    /*
     * @var ChannelRepository $channelRepository
     * */
    protected $channelRepository;

    /*
     * @var VideoRepository $videoRepository
     */
    protected $videoRepository;

    public function __construct(EntityManagerInterface $entityManager, ChannelRepository $channelRepository, VideoRepository $videoRepository)
    {
        $this->entityManager = $entityManager;
        $this->channelRepository = $channelRepository;
        $this->videoRepository = $videoRepository;
    }

    public function updateChannel(Channel $channel) : void
    {
        $channel->getId();

        $channelViewsMedian = $this->videoRepository->selectChannelFirstHourViewsMedian($channel->getId());

        if ($channelViewsMedian === 0) {
            // No channel views. Can't update video performance yet.
            return;
        }

        $videos = $channel->getUploadedVideos();
        foreach ($videos as $video) {
            $video->setPerformance($video->getFirstHourViews() / $channelViewsMedian);
        }

        $this->entityManager->persist($channel);
        $this->entityManager->flush();
    }

    public function updateAllChannels() : void
    {
        $channels = $this->channelRepository->findAll();
        foreach ($channels as $channel) {
            $this->updateChannel($channel);
        }
    }
}