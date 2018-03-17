<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Given there is a youtube video named :arg1
     */
    public function thereIsAYoutubeVideoNamed($arg1)
    {
        throw new PendingException();
    }

    /**
     * @When I scrape yutube video
     */
    public function iScrapeYutubeVideo()
    {
        throw new PendingException();
    }

    /**
     * @Then video likes are stored in database
     */
    public function videoLikesAreStoredInDatabase()
    {
        throw new PendingException();
    }

    /**
     * @Given there is a youtube channel named :arg1
     */
    public function thereIsAYoutubeChannelNamed($arg1)
    {
        throw new PendingException();
    }

    /**
     * @When I scrape youtube channel
     */
    public function iScrapeYoutubeChannel()
    {
        throw new PendingException();
    }

    /**
     * @Then likes for all videos in channel are stored in database
     */
    public function likesForAllVideosInChannelAreStoredInDatabase()
    {
        throw new PendingException();
    }

}
