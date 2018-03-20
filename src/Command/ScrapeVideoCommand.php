<?php
namespace App\Command;

use App\Exception\YoutubeNotFoundException;
use App\Service\Youtube\VideoService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class ScrapeVideoCommand extends Command
{
    protected $videoService;

    public function __construct(VideoService $videoService)
    {
        parent::__construct();

        $this->videoService = $videoService;
    }

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:scrape-video')
            // the short description shown while running "php bin/console list"
            ->setDescription('Downloads youtube video statistics')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to download youtube video statistics...')
            // configure an argument
            ->addArgument('videoId', InputArgument::REQUIRED, 'Id of youtube video to scrape')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $videoId = $input->getArgument("videoId");

        $output->writeln("Starting to download youtube video statistics.");
        $output->writeln("Downloading video ID: " . $videoId);

        $this->videoService->setVideoId($videoId);
        try {
            $likeCount = $this->videoService->getLikeCount();
            $tagsInline = $this->videoService->getTagsInline();
            $output->writeln("Like count: " . $likeCount);
            $output->writeln("Tags: " . $tagsInline);
        } catch (YoutubeNotFoundException $e) {
            $output->writeln("ERROR: Video with video ID: {$videoId} is not found");
        }
    }
}