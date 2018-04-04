<?php

namespace App\tests\Command;

use App\Command\ScrapeChannelCommand;
use App\Command\VideoPerformanceCommand;
use App\Entity\Channel;
use App\Service\Scraper\VideoPerformanceUpdaterService;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class VideoPerformanceCommandTest extends KernelTestCase
{
    /* @var Application */
    protected $application;
    /* @var VideoPerformanceUpdaterService */
    protected $videoPerformanceUpdaterService;
    /* @var CommandTester */
    protected $commandTester;
    /* @var string */
    protected $commandName;

    public function setUp()
    {
        $kernel = self::bootKernel();
        $this->application = new Application($kernel);
        $this->videoPerformanceUpdaterService = $this->createMock(VideoPerformanceUpdaterService::class);

        $this->application->add(new VideoPerformanceCommand(
            $this->videoPerformanceUpdaterService
        ));
        $command = $this->application->find('app:video-performance');
        $this->commandTester = new CommandTester($command);
        $this->commandName = $command->getName();
    }

    public function testCommand()
    {
        // GIVEN
        $this->videoPerformanceUpdaterService->method("updateAllChannels")->willReturn(null);
        $this->videoPerformanceUpdaterService->expects($this->once())
            ->method('updateAllChannels');

        // WHEN
        $this->commandTester->execute(array(
            'command'  => $this->commandName,
        ));
        $output = $this->commandTester->getDisplay();

        // THEN
        $this->assertContains('Video performance calculation started.', $output);
        $this->assertContains('Video performance calculation finished.', $output);
    }
}
