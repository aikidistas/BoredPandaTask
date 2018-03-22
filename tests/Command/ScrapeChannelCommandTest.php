<?php
// tests/Command/CreateUserCommandTest.php
namespace App\Tests\Command;

use App\Command\ScrapeChannelCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use App\Service\Youtube\ChannelService;
use App\Service\Youtube\PlaylistItemsService;
use App\Service\Youtube\VideoService;

class ScrapeChannelCommandTest extends KernelTestCase
{
    /* @var Application */
    protected $application;
    /* @var ChannelService */
    protected $channelServiceMock;
    /* @var PlaylistItemsService */
    protected $playlistItemsServiceMock;
    /* @var VideoService */
    protected $videoServiceMock;
    /* @var CommandTester */
    protected $commandTester;
    /* @var string */
    protected $commandName;

    public function setUp()
    {
        $kernel = self::bootKernel();
        $this->application = new Application($kernel);
        $this->channelServiceMock = $this->createMock(ChannelService::class);
        $this->playlistItemsServiceMock = $this->createMock(PlaylistItemsService::class);
        $this->videoServiceMock = $this->createMock(VideoService::class);

        $this->application->add(new ScrapeChannelCommand(
            $this->channelServiceMock,
            $this->playlistItemsServiceMock,
            $this->videoServiceMock
        ));
        $command = $this->application->find('app:scrape-channel');
        $this->commandTester = new CommandTester($command);
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
        $this->assertContains('Downloading videos statistics from channel ID: CHANNEL_ID_FROM_TEST', $output);
    }

    public function testCommandOutputsUploadsPlaylistId()
    {
        // GIVEN
        $channelUploadsPlaylistId = 'CHANNEL_UPLOADS_PLAYLIST_ID';
        $this->channelServiceMock->method("getUploadedVideoPlaylistId")->willReturn($channelUploadsPlaylistId);

        // WHEN
        $this->commandTester->execute(array(
            'command'  => $this->commandName,
            'channelId' => 'CHANNEL_ID_FROM_TEST',
        ));
        // the output of the command in the console
        $output = $this->commandTester->getDisplay();


        // THEN
        $this->assertContains('Channel uploads playlist ID: CHANNEL_UPLOADS_PLAYLIST_ID', $output);
    }

    public function testCommandOutputsListOfUploadedVideoId()
    {
        // GIVEN
        $channelUploadsVideosIdList = 'video1, video2, video3';
        $this->playlistItemsServiceMock->method("getVideoIdListAsText")->willReturn($channelUploadsVideosIdList);

        // WHEN
        $this->commandTester->execute(array(
            'command'  => $this->commandName,
            'channelId' => 'CHANNEL_ID_FROM_TEST',
        ));
        // the output of the command in the console
        $output = $this->commandTester->getDisplay();


        // THEN
        $this->assertContains('List of Video ID in playlist: video1, video2, video3', $output);
    }
}