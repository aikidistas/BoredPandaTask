<?php
/**
 * Created by PhpStorm.
 * User: Nerijus
 * Date: 3/25/2018
 * Time: 1:16 AM
 */

namespace App\Service\Scraper;

use App\Entity\Channel;
use App\Entity\Video;
use App\Exception\YoutubeNotFoundException;
use App\Repository\VideoRepository;
use App\Service\Youtube\VideoService;
use App\Service\Youtube\PlaylistItemsService;
use Doctrine\ORM\EntityManagerInterface;

class VideoScraperService
{
    protected $videoService;
    protected $videoRepository;
    protected $entityManager;

    public function __construct(VideoService $videoService, VideoRepository $videoRepository,
                                EntityManagerInterface $entityManager)
    {
        $this->videoService = $videoService;
        $this->videoRepository = $videoRepository;
        $this->entityManager = $entityManager;
    }



    /**
     * @param string $videoId
     * @return Video
     * @throws YoutubeNotFoundException
     */
    public function scrapeVideo(string $videoId, Channel $channel) : Video
    {
        $video = $this->findVideoInDatabase($videoId);
        if ($video instanceof Video) {
            // video was already downloaded from youtube earlier. Need to update video statistics
            $video = $this->videoService->getUpdatedVideoEntity($video);

            return $video;
        }

        // download new video
        $this->videoService->setVideoId($videoId);
        $video = $this->videoService->getVideoEntity();
        $channel->addUploadedVideo($video);

        return $video;
    }

    private function findVideoInDatabase(string $videoId) : ? Video
    {
        return $this->videoRepository->find($videoId);
    }
}