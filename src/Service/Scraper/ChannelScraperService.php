<?php
/**
 * Created by PhpStorm.
 * User: Nerijus
 * Date: 3/25/2018
 * Time: 1:16 AM
 */

namespace App\Service\Scraper;

use App\Entity\Channel;
use App\Exception\YoutubeNotFoundException;
use App\Repository\ChannelRepository;
use App\Service\Youtube\ChannelService;
use App\Service\Youtube\PlaylistItemsService;
use App\Service\Youtube\VideoService;
use Doctrine\ORM\EntityManagerInterface;

class ChannelScraperService
{
    protected $channelService;
    protected $channelRepository;
    protected $playlistItemsService;
    protected $videoScraperService;
    protected $entityManager;

    public function __construct(ChannelService $channelService, ChannelRepository $channelRepository,
                                PlaylistItemsService $playlistItemsService, VideoScraperService $videoScraperService,
                                EntityManagerInterface $entityManager)
    {
        $this->channelService = $channelService;
        $this->channelRepository = $channelRepository;
        $this->playlistItemsService = $playlistItemsService;
        $this->videoScraperService = $videoScraperService;
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $channelId
     * @return Channel
     * @throws YoutubeNotFoundException
     */
    public function scrapeChannel(string $channelId) : Channel
    {
        $channel = $this->findChannelInDatabase($channelId);
        if ($channel instanceof Channel) {
            // channel already downloaded from youtube. No need to redownload
            return $channel;
        }

        $this->channelService->setChannelId($channelId);
        $channel = $this->channelService->getChannelEntity();

        $this->entityManager->persist($channel);
        $this->entityManager->flush();

        return $channel;
    }

    public function scrapeChannelPlaylist(Channel $channel)
    {
        $playlistId = $channel->getUploadsPlaylistId();
        $this->playlistItemsService->setPlaylistId($playlistId);
        $videoIdArray = $this->playlistItemsService->getVideoIdArray();
        foreach ($videoIdArray as $videoId) {
            try {
                $this->videoScraperService->scrapeVideo($videoId, $channel);
            } catch (YoutubeNotFoundException $e) {
                // private video info can't be downloaded
            }
        }

        $this->entityManager->persist($channel);
        $this->entityManager->flush();
    }

    private function findChannelInDatabase(string $channelId) : ? Channel
    {
        return $this->channelRepository->find($channelId);
    }
}