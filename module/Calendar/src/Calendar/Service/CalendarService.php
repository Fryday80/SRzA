<?php
namespace Calendar\Service;

use Google_Service_Calendar;
use Google_Client;

class CalendarService {
    private $APPLICATION_NAME;
    private $CREDENTIALS_PATH;
    private $CLIENT_SECRET_PATH;
    private $SCOPES;
    private $gCalendarService;

    function __construct($serviceManager) {
        $confPath = __DIR__.'/../../../config/';
        $this->APPLICATION_NAME = 'SRA Events';
        $this->CREDENTIALS_PATH = $confPath.'calendar-php-quickstart.json';
        $this->CLIENT_SECRET_PATH = $confPath.'client_secret.json';
        // If modifying these scopes, delete your previously saved credentials
        $this->SCOPES = implode(' ', array(
                Google_Service_Calendar::CALENDAR_READONLY)
        );
        // Get the API client and construct the service object.
        $client = $this->getClient();
        $this->gCalendarService = new Google_Service_Calendar($client);
    }
    public function getEventsFrom($start, $end) {
        // Print the next 10 events on the user's calendar.
        $calendarId = 'primary';
        $optParams = array(
            'maxResults' => 10,
            'orderBy' => 'startTime',
            'singleEvents' => TRUE,
            'timeMin' => date('c'),
        );
        $results = $this->gCalendarService->events->listEvents($calendarId, $optParams);
        return $results;
    }

    /**
     * Returns an authorized API client.
     * @return Google_Client the authorized client object
     */
    function getClient() {
        $client = new Google_Client();
        $client->setApplicationName($this->APPLICATION_NAME);
        $client->setScopes($this->SCOPES);
        $client->setAuthConfig($this->CLIENT_SECRET_PATH);
        $client->setAccessType('offline');

        // Load previously authorized credentials from a file.
        $credentialsPath = $this->expandHomeDirectory($this->CREDENTIALS_PATH);

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

    /**
     * Expands the home directory alias '~' to the full path.
     * @param string $path the path to expand.
     * @return string the expanded path.
     */
    function expandHomeDirectory($path) {
        $homeDirectory = getenv('HOME');
        print($homeDirectory);
        if (empty($homeDirectory)) {
            $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
        }
        print('<br>');
        print($homeDirectory);
        print('<br>');
        return str_replace('~', realpath($homeDirectory), $path);
    }
}
