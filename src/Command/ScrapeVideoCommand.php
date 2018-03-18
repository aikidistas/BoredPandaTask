<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Google_Client;
use Google_Service_YouTube;

class ScrapeVideoCommand extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:scrape-video')

            // the short description shown while running "php bin/console list"
            ->setDescription('Downloads youtube video statistics')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to download youtube video statistics...')

            // configure an argument
            ->addArgument('videoId', InputArgument::REQUIRED, 'Id of youtube video to scrape')

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("You are trying to download youtube video statistics. This feature is not yet implemented");
        $output->writeln("Downloading video ID: " . $input->getArgument("videoId"));

        $client = new Google_Client();
        $client->setApplicationName("BoredPanda Task");
        $client->setDeveloperKey("AIzaSyDBx0tNuwSdIAJ27L-fml8zJsMW337c0Bg");

        //$client = getClient();
        $service = new Google_Service_YouTube($client);

        $this->videosListById($service,
            'snippet,contentDetails,statistics',
            array('id' => 'Ks-_Mh1QhMc'));

    }

    function videosListById($service, $part, $params) {
        $params = array_filter($params);
        $response = $service->videos->listVideos(
            $part,
            $params
        );

        print_r($response);
    }


    function getClient() {
        $client = new Google_Client();
        $client->setApplicationName('API Samples');
        $client->setScopes('https://www.googleapis.com/auth/youtube.force-ssl');
        // Set to name/location of your client_secrets.json file.
        $client->setAuthConfig('client_secrets.json');
        $client->setAccessType('offline');

        // Load previously authorized credentials from a file.
        $credentialsPath = expandHomeDirectory(CREDENTIALS_PATH);
        if (file_exists($credentialsPath)) {
            $accessToken = json_decode(file_get_contents($credentialsPath), true);
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

            // Store the credentials to disk.
            if(!file_exists(dirname($credentialsPath))) {
                mkdir(dirname($credentialsPath), 0700, true);
            }
            file_put_contents($credentialsPath, json_encode($accessToken));
            printf("Credentials saved to %s\n", $credentialsPath);
        }
        $client->setAccessToken($accessToken);

        // Refresh the token if it's expired.
        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
        }
        return $client;
    }

}