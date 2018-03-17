**BoredPanda interview task 2018-03**

**Requirements**

You need to create a simple application which:
1. Using Youtube API (https://developers.google.com/youtube/v3/) scrapes channel
videos with tags and stats. Also you need to track changes of video stats every N
minutes in order to see how videos are performing. Please pick the interval to scan stats
which, according to you, is efficient and smart. You can hardcode channel ID in code,
that’s not important.
2. Create DB scheme and save scraped data. Please consider, that we will want to scan a
lot of channels, so queries to aggregate and select data shouldn’t take long. If you would
like to use more than MySQL, like Redis or database sharding, you can just give us notes
how you would do that, no need to implement this. Using just MySQL should be ok for
this task.
3. Create mini dashboard (no requirements for UI), where you can filter videos:
a) By tags (use autocomplete input).
b) By video performance (first hour views divided by channels all videos first hour
views median)
Bonus points for pseudo algorithm for fetching as many youtube channels as possible.
Requirements:
PHP or Python; (You can use framework if needed)
MySQL; (If you use ORM, please write at least 1 plain SQL query)
MVC structure;
GitHub;
After finishing task, please send us the GitHub repository link.
Good Luck :)

**Usage instructions**
1. Install dependencies using _php composer.phar install_
2. Run BDD (behat) tests using: _bin/behat_ 
