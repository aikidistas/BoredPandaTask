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

**Set Up instructions**
1. Create `.env_ file using _.env.dist`
2. Install dependencies using `php composer.phar install`
3. Database:\
3.1 Configure the driver (mysql) and server_version (5.6) in `config/packages/doctrine.yaml`\
3.2 Create database specified in `.env` by running: `php bin/console doctrine:database:create`\
3.3 Create tables and demo data by running: `php bin/console doctrine:migrations:migrate`
4. Tests:\
4.1 Run BDD (behat) tests using: `bin/behat`\
4.2 Run phpUnit tests using: `bin/simple-phpunit`\
4.3 Generate phpUnit code coverage: `bin/simple-phpunit --coverage-html=coverage`\
4.4 Look code coverage in the browser`coverage/index.html`
5. Run web server using: `bin/console server:run`

**Usage instructions. Scraper commands**
1. `bin/console app:scrape-video VIDEO_ID_HERE`\
1.1 Example: `bin/console app:scrape-video Ks-_Mh1QhMc` 
2. `bin/console app:scrape-channel CHANNEL_ID_HERE`\
2.1 Example: `bin/console app:scrape-channel UCydKucK3zAWRuHKbB4nJjtw`

**Notes**

**TODO: remove files from bin folder, that will be installed by composer**

1. Asumed that only uploaded videos in the channel need to be scraped. Possible to add also other playlists in the channel. Should be implemented separately.

**temporary info:**\
PL_hNADyE5fYAAiS9JYnN_BCgTlY_OU2sE	-	playlist		34\
									-	playlistTitle	150\
									\
UCydKucK3zAWRuHKbB4nJjtw			-	channel			24\
									-	channelTitle	100\
									\
riTQ5m1GaQs							-	video			11\
\
VVV5ZEt1Y0szekFXUnVIS2JCNG5KanR3LnpXYW9lX0hDaUNB	playlistitem	48
