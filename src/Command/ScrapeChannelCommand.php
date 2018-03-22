<?php
namespace App\Command;

use App\Exception\YoutubeNotFoundException;
use App\Service\Youtube\ChannelService;
use App\Service\Youtube\PlaylistItemsService;
use App\Service\Youtube\PlaylistService;
use App\Service\Youtube\VideoService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class ScrapeChannelCommand extends Command
{
    protected $channelService;
    protected $playlistItemsService;
//    protected $playlistService;
    protected $videoService;

    public function __construct(ChannelService $channelService, PlaylistItemsService $playlistItemsService,
                                /*PlaylistService $playlistService, */VideoService $videoService)
    {
        parent::__construct();

        $this->channelService = $channelService;
        $this->playlistItemsService = $playlistItemsService;
//        $this->playlistService = $playlistService;
        $this->videoService = $videoService;
    }

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:scrape-channel')
            // the short description shown while running "php bin/console list"
            ->setDescription('Downloads youtube statistics for all videos in given channel')
            // the full command description shown when running the command with the "--help" option
            ->setHelp('This command allows you to download youtube videos statistics for given channel...')
            // configure an argument
            ->addArgument('channelId', InputArgument::REQUIRED, 'Id of youtube channel to scrape')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $channelId = $input->getArgument("channelId");
        $output->writeln("Downloading videos statistics from channel ID: " . $channelId);
        $output->writeln("");

        $this->channelService->setChannelId($channelId);
        try {
            $uploadsPlaylistId = $this->channelService->getUploadedVideoPlaylistId();

//        $this->playlistService->setChannelId($channelId);
//        $playlistsIdArray = $this->playlistService->getPlaylistIdArray();

//        foreach ($playlistsIdArray as $playlistId) {
            $playlistId = $uploadsPlaylistId;
            $output->writeln("Channel uploads playlist ID: " . $playlistId);
            $this->playlistItemsService->setPlaylistId($playlistId);
            $videoIdListText = $this->playlistItemsService->getVideoIdListAsText();
            $output->writeln("List of Video ID in playlist: " . $videoIdListText);

            $videoIdArray = $this->playlistItemsService->getVideoIdArray();
            foreach ($videoIdArray as $videoId) {
                $this->videoService->setVideoId($videoId);
                try {
                    $videoTagsText = $this->videoService->getTagsInline();
                    $videoLikeCount = $this->videoService->getLikeCount();
                    $output->writeln("");
                    $output->writeln("Video ID: " . $videoId);
                    $output->writeln("Like count: " . $videoLikeCount);
                    $output->writeln("Tags: " . $videoTagsText);
                } catch (YoutubeNotFoundException $e) {
                    $output->writeln("");
                    $output->writeln("Video ID: " . $videoId . ". NOT FOUND!");
                    // this could be a private video in the playlist that you can't see
                    // just skip not found video in this demo application.
                    // Would log it in real life
                }
            }
//        }

//        $uploadsPlaylistId = $this->channelService->getUploadedVideoPlaylistId();
//        $output->writeln("Channel uploads playlist ID: " . $uploadsPlaylistId);
        } catch (YoutubeNotFoundException $e) {
            $output->writeln("Uploads playlist not found in the cahnnel");
        }
    }
}