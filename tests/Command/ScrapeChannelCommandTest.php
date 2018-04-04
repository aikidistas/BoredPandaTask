<?php

namespace App\Tests\Command;

use App\Command\ScrapeChannelCommand;
use App\Entity\Channel;
use App\Entity\Tag;
use App\Entity\VersionedLike;
use App\Entity\VersionedView;
use App\Entity\Video;
use App\Exception\YoutubeNotFoundException;
use App\Service\Scraper\ChannelScraperService;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ScrapeChannelCommandTest extends KernelTestCase
{
    /* @var Application */
    protected $application;
    /* @var ChannelScraperService */
    protected $channelScraperService;
    /* @var CommandTester */
    protected $commandTester;
    /* @var string */
    protected $commandName;

    public function setUp()
    {
        $kernel = self::bootKernel();
        $this->application = new Application($kernel);
        $this->channelScraperService = $this->createMock(ChannelScraperService::class);

        $this->application->add(new ScrapeChannelCommand(
            $this->channelScraperService
        ));
        $command = $this->application->find('app:scrape-channel');
        $this->commandTester = new CommandTester($command);
        $this->commandName = $command->getName();
    }

    public function testCommandTakesChannelIdParameter()
    {
        // GIVEN
        $channelIdInput = 'CHANNEL_ID_FROM_TEST';
        $channelMock = $this->createMock(Channel::class);
        $this->channelScraperService->method("scrapeChannel")->willReturn($channelMock);
        $channelMock->method("getTitle")->willReturn("CHANNEL_TITLE");
        $channelMock->method("getUploadedVideos")->willReturn(new ArrayCollection([]));

        // WHEN
        $this->commandTester->execute(array(
            'command'  => $this->commandName,
            'channelId' => $channelIdInput,
        ));
        $output = $this->commandTester->getDisplay();

        // THEN
        $this->assertContains('Downloading videos statistics from channel ID: CHANNEL_ID_FROM_TEST', $output);
        $this->assertContains('Channel Title: CHANNEL_TITLE', $output);
    }

    public function testCommandCantFindChannel()
    {
        // GIVEN
        $channelIdInput = 'CHANNEL_ID_FROM_TEST';
        $channelMock = $this->createMock(Channel::class);
        $this->channelScraperService->method("scrapeChannel")->will($this->throwException(new YoutubeNotFoundException()));

        // WHEN
        $this->commandTester->execute(array(
            'command'  => $this->commandName,
            'channelId' => $channelIdInput,
        ));
        $output = $this->commandTester->getDisplay();

        // THEN
        $this->assertContains("Could not find information about the channel using youtube api", $output);
    }

    public function testCommandOutputsChannelUploadedVideosInfo()
    {
        // GIVEN
        $channelMock = $this->createMock(Channel::class);
        $this->channelScraperService->method("scrapeChannel")->willReturn($channelMock);
        $video1Mock = $this->createMock(Video::class);
        $video2Mock = $this->createMock(Video::class);
        $channelMock->method("getUploadedVideos")->willReturn(new ArrayCollection([$video1Mock, $video2Mock]));
        $versionedLike1Mock = $this->createMock(VersionedLike::class);
        $versionedLike2Mock = $this->createMock(VersionedLike::class);
        $versionedView1Mock = $this->createMock(VersionedView::class);
        $versionedView2Mock = $this->createMock(VersionedView::class);
        $tag11Mock = $this->createMock(Tag::class);
        $tag12Mock = $this->createMock(Tag::class);
        $tag21Mock = $this->createMock(Tag::class);
        $tag22Mock = $this->createMock(Tag::class);

        $video1Mock->method('getId')->willReturn('VIDEO_1_ID');
        $video2Mock->method('getId')->willReturn('VIDEO_2_ID');
        $video1Mock->method('getTitle')->willReturn('VIDEO_1_TITLE');
        $video2Mock->method('getTitle')->willReturn('VIDEO_2_TITLE');
        $video1Mock->method('getVersionedLikes')->willReturn(new ArrayCollection([$versionedLike1Mock]));
        $video2Mock->method('getVersionedLikes')->willReturn(new ArrayCollection([$versionedLike2Mock]));
        $versionedLike1Mock->method('getAmount')->willReturn(100);
        $versionedLike2Mock->method('getAmount')->willReturn(200);
        $video1Mock->method('getVersionedViews')->willReturn(new ArrayCollection([$versionedView1Mock]));
        $video2Mock->method('getVersionedViews')->willReturn(new ArrayCollection([$versionedView2Mock]));
        $versionedView1Mock->method('getAmount')->willReturn(1000);
        $versionedView2Mock->method('getAmount')->willReturn(2000);
        $video1Mock->method('getTags')->willReturn(new ArrayCollection([$tag11Mock, $tag12Mock]));
        $video2Mock->method('getTags')->willReturn(new ArrayCollection([$tag21Mock, $tag22Mock]));
        $tag11Mock->method('getText')->willReturn('TAG_1_1');
        $tag12Mock->method('getText')->willReturn('TAG_1_2');
        $tag21Mock->method('getText')->willReturn('TAG_2_1');
        $tag22Mock->method('getText')->willReturn('TAG_2_2');


        // WHEN
        $this->commandTester->execute(array(
            'command'  => $this->commandName,
            'channelId' => 'CHANNEL_ID_FROM_TEST',
        ));
        // the output of the command in the console
        $output = $this->commandTester->getDisplay();


        // THEN
        $this->assertContains('Video ID: VIDEO_1_ID', $output);
        $this->assertContains('Video title: VIDEO_1_TITLE', $output);
        $this->assertContains('Like count: 100', $output);
        $this->assertContains('View count: 1000', $output);
        $this->assertContains('Tags: TAG_1_1, TAG_1_2', $output);

        $this->assertContains('Video ID: VIDEO_2_ID', $output);
        $this->assertContains('Video title: VIDEO_2_TITLE', $output);
        $this->assertContains('Like count: 200', $output);
        $this->assertContains('View count: 2000', $output);
        $this->assertContains('Tags: TAG_2_1, TAG_2_2', $output);
    }
}