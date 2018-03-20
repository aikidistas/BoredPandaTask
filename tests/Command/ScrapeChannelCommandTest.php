<?php
// tests/Command/CreateUserCommandTest.php
namespace App\Tests\Command;

use App\Command\ScrapeChannelCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use App\Service\Youtube\ChannelService;
class ScrapeChannelCommandTest extends KernelTestCase
{
    /* @var Application */
    protected $application;
    /* @var ChannelService */
    protected $channelServiceMock;
    /* @var CommandTester */
    protected $commandTester;
    /* @var string */
    protected $commandName;

    public function setUp()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $channelServiceMock = $this->createMock(ChannelService::class);
        $application->add(new ScrapeChannelCommand($channelServiceMock));
        $command = $application->find('app:scrape-channel');
        $commandTester = new CommandTester($command);


        $this->application  = $application;
        $this->channelServiceMock = $channelServiceMock;
        $this->commandTester = $commandTester;
        $this->commandName = $command->getName();
    }

    public function testCommandTakesChannelIdParameter()
    {
        // GIVEN
        $channelIdInput = 'CHANNEL_ID_FROM_TEST';

        // WHEN
        $this->commandTester->execute(array(
            'command'  => $this->commandName,
            'channelId' => $channelIdInput,
        ));
        $output = $this->commandTester->getDisplay();

        // THEN
        $this->assertContains('Downloading videos from channel ID: CHANNEL_ID_FROM_TEST', $output);
    }

    public function testCommandReturnsListOfVideoIdInTheChannelFromChannelService()
    {
        // GIVEN
        $expectedVideoIdText = 'firstId, secondId, thirdId';
        $this->channelServiceMock->method("getVideoIdListAsText")->willReturn($expectedVideoIdText);

        // WHEN
        $this->commandTester->execute(array(
            'command'  => $this->commandName,
            'channelId' => 'CHANNEL_ID_FROM_TEST',
        ));
        // the output of the command in the console
        $output = $this->commandTester->getDisplay();


        // THEN
        $this->assertContains('List of uploaded videos ID in channel: firstId, secondId, thirdId', $output);
    }
}