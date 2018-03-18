<?php
// tests/Command/CreateUserCommandTest.php
namespace App\Tests\Command;

use App\Command\ScrapeChannelCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ScrapeChannelCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $application->add(new ScrapeChannelCommand());

        $command = $application->find('app:scrape-channel');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),

            // pass arguments to the helper
            'channelId' => 'CHANNEL_ID_FROM_TEST',
        ));

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertContains('Downloading videos from channel ID: CHANNEL_ID_FROM_TEST', $output);
    }
}