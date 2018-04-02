<?php


namespace App\Service\Scraper;


use App\Entity\Channel;
use App\Repository\ChannelRepository;
use Doctrine\ORM\EntityManagerInterface;

class VideoPerformanceUpdaterService
{
    /*
     * @var ChannelRepository $channelRepository
     * */
    protected $channelRepository;

    public function __construct(ChannelRepository $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }

    public function updateChannel(Channel $channel) : void
    {
        // TODO: update this...
    }

    public function updateAllChannels() : void
    {
        $channels = $this->channelRepository->findAll();
        foreach ($channels as $channel) {
            $this->updateChannel($channel);
        }
    }
}