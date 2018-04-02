<?php

namespace App\Command;

use App\Service\Scraper\VideoPerformanceUpdaterService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VideoPerformanceCommand extends Command
{
    protected static $defaultName = 'app:video-performance';
    protected $videoPerformanceUpdaterService;


    public function __construct(VideoPerformanceUpdaterService $videoPerformanceUpdaterService)
    {
        parent::__construct();
        $this->videoPerformanceUpdaterService = $videoPerformanceUpdaterService;
    }

    protected function configure()
    {
        $this->setDescription('Recalculate each video performance value (first hour views divided by channels all videos first hour views median).');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Video performance calculation started.');

        $this->videoPerformanceUpdaterService->updateAllChannels();

        $output->writeln('Video performance calculation finished.');
    }
}
