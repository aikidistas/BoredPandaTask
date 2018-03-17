Feature: Youtube scraper
  In order to analyze youtube trends
  As bored panda analyst
  I need to download youtube video channel statistics to local database for further analysis

  Scenario: Downloading a single youtube video statistics
    Given there is a youtube video named "5 Weird Mysteries Solved By The Internet"
    When I scrape yutube video
    Then video likes are stored in database

  Scenario: Downloading all youtube video statistics in one channel
    Given there is a youtube channel named "Bored Panda"
    When I scrape youtube channel
    Then likes for all videos in channel are stored in database