<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class ScrapeVideoCommand extends Command
{
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
        $output->writeln("You are trying to download youtube video statistics. This feature is not yet implemented");
        $output->writeln("Downloadable video ID:" . $input->getArgument("videoId"));
    }
}