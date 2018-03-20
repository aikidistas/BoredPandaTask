<?php
// tests/Command/CreateUserCommandTest.php
namespace App\Tests\Command;

use App\Command\ScrapeVideoCommand;
use App\Service\Youtube\VideoService;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ScrapeVideoCommandTest extends KernelTestCase
{

    /* @var Application */
    protected $application;
    /* @var VideoService */
    protected $videoServiceMock;
    /* @var CommandTester */
    protected $commandTester;
    /* @var string */
    protected $commandName;

    public function setUp()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $videoServiceMock = $this->createMock(VideoService::class);
        $application->add(new ScrapeVideoCommand($videoServiceMock));
        $command = $application->find('app:scrape-video');
        $commandTester = new CommandTester($command);


        $this->application  = $application;
        $this->videoServiceMock = $videoServiceMock;
        $this->commandTester = $commandTester;
        $this->commandName = $command->getName();
    }

    public function testCommandTakesVideoIdParameter()
    {
        // GIVEN
        $videoIdInput = 'VIDEO_ID_FROM_TEST';

        // WHEN
        $this->commandTester->execute(array(
            'command'  => $this->commandName,
            'videoId' => $videoIdInput,
        ));
        $output = $this->commandTester->getDisplay();

        // THEN
        $this->assertContains('Downloading video ID: VIDEO_ID_FROM_TEST', $output);
    }

    public function testCommandReturnsLikeCountFromYoutubeVideoService()
    {
        // GIVEN
        $this->videoServiceMock->method("getLikeCount")->willReturn(1234);


        // WHEN
        $this->commandTester->execute(array(
            'command'  => $this->commandName,
            'videoId' => 'VIDEO_ID_FROM_TEST',
        ));
        // the output of the command in the console
        $output = $this->commandTester->getDisplay();


        // THEN
        $this->assertContains('Like count: 1234', $output);
    }

    public function testCommandReturnsTagsFromYoutubeVideoService()
    {
        // GIVEN
        $this->videoServiceMock->method("getTagsInline")->willReturn('Tag1, Tag2, Best video tag 3');

        // WHEN
        $this->commandTester->execute(array(
            'command'  => $this->commandName,
            'videoId' => 'VIDEO_ID_FROM_TEST',
        ));
        // the output of the command in the console
        $output = $this->commandTester->getDisplay();

        // THEN
        $this->assertContains('Tags: Tag1, Tag2, Best video tag 3', $output);
    }
}