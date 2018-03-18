<?php
// tests/Command/CreateUserCommandTest.php
namespace App\Tests\Command;

use App\Command\ScrapeVideoCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ScrapeVideoCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $application->add(new ScrapeVideoCommand());

        $command = $application->find('app:scrape-video');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),

            // pass arguments to the helper
            'videoId' => 'VIDEO_ID_FROM_TEST',
        ));

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertContains('Downloading video ID: VIDEO_ID_FROM_TEST', $output);
    }
}