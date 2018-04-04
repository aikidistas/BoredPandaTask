<?php
namespace App\Command;

use App\Exception\YoutubeNotFoundException;
use App\Service\Scraper\ChannelScraperService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class ScrapeChannelCommand extends Command
{
    protected $channelScraperService;

    public function __construct(ChannelScraperService $channelScraperService)
    {
        parent::__construct();

        $this->channelScraperService = $channelScraperService;
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

        try {
            $channel = $this->channelScraperService->scrapeChannel($channelId);
            $output->writeln("Channel Title: " . $channel->getTitle());
            $this->channelScraperService->scrapeChannelPlaylist($channel);

            foreach ($channel->getUploadedVideos() as $video)
            {
                $output->writeln("");
                $output->writeln("Video ID: " . $video->getId());
                $output->writeln("Video title: " . $video->getTitle());
                $output->writeln("");
                $output->writeln("Like count: " . $video->getVersionedLikes()->last()->getAmount());
                $output->writeln("View count: " . $video->getVersionedViews()->last()->getAmount());
                $tags = $video->getTags();
                $tagsArray = [];
                foreach ($tags as $tag) {
                    $tagsArray [] = $tag->getText();
                }
                $output->writeln("");
                $output->writeln("Tags: " . implode( ", ", $tagsArray));
            }



        } catch (YoutubeNotFoundException $e) {
            $output->writeln("Could not find information about the channel using youtube api");
        }
    }
}