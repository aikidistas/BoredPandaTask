<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class ScrapeChannelCommand extends Command
{
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
        $output->writeln("You are trying to download youtube video statistics for a given channel. This feature is not yet implemented");
        $output->writeln("Downloadable channel ID:" . $input->getArgument("channelId"));
    }
}