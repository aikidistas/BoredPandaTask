<?php

namespace App\Tests\Service\Youtube;

use App\Service\Youtube\VideoService;
use Google_Service_YouTube;
use BadFunctionCallException;
use Google_Service_YouTube_Resource_Videos;
use Google_Service_YouTube_VideoListResponse;
use Google_Service_YouTube_Video;
use Google_Service_YouTube_VideoStatistics;
use Google_Service_YouTube_VideoSnippet;
use App\Exception\YoutubeNotFoundException;

class VideoServiceTest extends \PHPUnit_Framework_TestCase
{
    /* @var Google_Service_YouTube */
    protected $youtubeServiceMock;
    /* @var VideoService */
    protected $videoService;

    public function setUp()
    {
        $this->youtubeServiceMock = $this->createMock(Google_Service_YouTube::class);
        $this->videoService = new VideoService($this->youtubeServiceMock);
    }

    public function testSetVideoId()
    {
        $this->videoService->setVideoId("VIDEO_ID");
    }

    /* @throws YoutubeNotFoundException */
    public function testGetLikeCount_throwsException_WhenVideoIdNotSet()
    {
        // SET UP
        $this->expectException(BadFunctionCallException::class);

        // GIVEN
        // VideoId is not set

        // WHEN
        $this->videoService->getLikeCount();

        // THEN
        // exception is thrown
    }

    /* @throws YoutubeNotFoundException */
    public function testGetTags_throwsException_WhenVideoIdNotSet()
    {
        // SET UP
        $this->expectException(BadFunctionCallException::class);

        // GIVEN
        // VideoId is not set

        // WHEN
        $this->videoService->getTags();

        // THEN
        // exception is thrown
    }

    /* @throws YoutubeNotFoundException */
    public function testGetTagsInline_throwsException_WhenVideoIdNotSet()
    {
        // SET UP
        $this->expectException(BadFunctionCallException::class);

        // GIVEN
        // VideoId is not set

        // WHEN
        $this->videoService->getTagsInline();

        // THEN
        // exception is thrown
    }

    /* @throws YoutubeNotFoundException */
    public function testGetLikeCount_returnsLikeCountFromGoogleService()
    {
        // SET UP
        $resourceVideosMock = $this->createMock(Google_Service_YouTube_Resource_Videos::class);
        $responseMock = $this->createMock(Google_Service_YouTube_VideoListResponse::class);
        $responseVideoItemMock = $this->createMock(Google_Service_YouTube_Video::class);
        $responseVideoStatisticsMock = $this->createMock(Google_Service_YouTube_VideoStatistics::class);
        $this->youtubeServiceMock->videos = $resourceVideosMock;

        $resourceVideosMock->method('listVideos')->willReturn($responseMock);
        $responseMock->method('getItems')->willReturn(array($responseVideoItemMock));
        $responseVideoItemMock->method('getStatistics')->willReturn($responseVideoStatisticsMock);

        // GIVEN
        $expectedLikeCount = 4321;
        $responseVideoStatisticsMock->method('getLikeCount')->willReturn($expectedLikeCount);
        $this->videoService->setVideoId("VIDEO_ID");

        // WHEN
        $actualLikeCount = $this->videoService->getLikeCount();

        // THEN
        $this->assertEquals($expectedLikeCount, $actualLikeCount, "Wrong like count returned from VideoService");
    }

    /* @throws YoutubeNotFoundException */
    public function testGetLikeCount_returnsZeroItemsFoundFromGoogleService()
    {
        // SET UP
        $resourceVideosMock = $this->createMock(Google_Service_YouTube_Resource_Videos::class);
        $responseMock = $this->createMock(Google_Service_YouTube_VideoListResponse::class);
        $this->youtubeServiceMock->videos = $resourceVideosMock;
        $resourceVideosMock->method('listVideos')->willReturn($responseMock);

        $this->expectException(YoutubeNotFoundException::class);
        $this->videoService->setVideoId("VIDEO_ID");

        // GIVEN
        $responseMock->method('getItems')->willReturn(array());

        // WHEN
        $this->videoService->getLikeCount();

        // THEN
        // not found exception is thrown
    }

    /* @throws YoutubeNotFoundException */
    public function testGetTags_returnsTagsArrayFromGoogleService()
    {
        // SET UP
        $resourceVideosMock = $this->createMock(Google_Service_YouTube_Resource_Videos::class);
        $responseMock = $this->createMock(Google_Service_YouTube_VideoListResponse::class);
        $responseVideoItemMock = $this->createMock(Google_Service_YouTube_Video::class);
        $responseVideoSnippetMock = $this->createMock(Google_Service_YouTube_VideoSnippet::class);
        $this->youtubeServiceMock->videos = $resourceVideosMock;

        $resourceVideosMock->method('listVideos')->willReturn($responseMock);
        $responseMock->method('getItems')->willReturn(array($responseVideoItemMock));
        $responseVideoItemMock->method('getSnippet')->willReturn($responseVideoSnippetMock);

        // GIVEN
        $expectedTags = array('CoolTag1', 'JustAnotherTag', 'OneMoreTag');
        $responseVideoSnippetMock->method('getTags')->willReturn($expectedTags);
        $this->videoService->setVideoId("VIDEO_ID");

        // WHEN
        $actualTags = $this->videoService->getTags();

        // THEN
        $this->assertEquals($expectedTags, $actualTags, "Wrong tags returned from VideoService");
    }

    /* @throws YoutubeNotFoundException */
    public function testGetTags_returnsZeroItemsFoundFromGoogleService()
    {
        // SET UP
        $resourceVideosMock = $this->createMock(Google_Service_YouTube_Resource_Videos::class);
        $responseMock = $this->createMock(Google_Service_YouTube_VideoListResponse::class);
        $this->youtubeServiceMock->videos = $resourceVideosMock;
        $resourceVideosMock->method('listVideos')->willReturn($responseMock);

        $this->expectException(YoutubeNotFoundException::class);
        $this->videoService->setVideoId("VIDEO_ID");

        // GIVEN
        $responseMock->method('getItems')->willReturn(array());

        // WHEN
        $this->videoService->getTags();

        // THEN
        // not found exception is thrown
    }

    /* @throws YoutubeNotFoundException */
    public function testGetTagsInline_returnsTagsArrayFromGoogleService()
    {
        // SET UP
        $resourceVideosMock = $this->createMock(Google_Service_YouTube_Resource_Videos::class);
        $responseMock = $this->createMock(Google_Service_YouTube_VideoListResponse::class);
        $responseVideoItemMock = $this->createMock(Google_Service_YouTube_Video::class);
        $responseVideoSnippetMock = $this->createMock(Google_Service_YouTube_VideoSnippet::class);
        $this->youtubeServiceMock->videos = $resourceVideosMock;

        $resourceVideosMock->method('listVideos')->willReturn($responseMock);
        $responseMock->method('getItems')->willReturn(array($responseVideoItemMock));
        $responseVideoItemMock->method('getSnippet')->willReturn($responseVideoSnippetMock);

        // GIVEN
        $expectedTagsArray = array('CoolTag1', 'JustAnotherTag', 'OneMoreTag');
        $responseVideoSnippetMock->method('getTags')->willReturn($expectedTagsArray);
        $this->videoService->setVideoId("VIDEO_ID");

        // WHEN
        $actualTags = $this->videoService->getTagsInline();

        // THEN
        $expectedTagsText = 'CoolTag1, JustAnotherTag, OneMoreTag';
        $this->assertEquals($expectedTagsText, $actualTags, "Wrong tags returned from VideoService");
    }

}
