<?php

namespace App\Service\Scraper;

use App\Entity\Channel;
use App\Entity\Video;
use App\Exception\YoutubeNotFoundException;
use App\Repository\VideoRepository;
use App\Service\Youtube\VideoService;
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
        } else {
            // download new video
            $this->videoService->setVideoId($videoId);
            $video = $this->videoService->getVideoEntity();
            $channel->addUploadedVideo($video);
        }


        $this->entityManager->persist($video);
        $this->entityManager->flush();

        return $video;
    }

    private function findVideoInDatabase(string $videoId) : ? Video
    {
        return $this->videoRepository->find($videoId);
    }
}