<?php
namespace Calendar\Service;

class CalendarService {
    private $calendar_service;

    function __construct($serviceManager) {
        define('APPLICATION_NAME', 'Google Calendar API PHP Quickstart');
        define('CREDENTIALS_PATH', '~/.credentials/calendar-php-quickstart.json');
        define('CLIENT_SECRET_PATH', __DIR__ . '/../../../config/client_secret.json');

        // Get the API client and construct the service object.
        $client = $this->getClient();
    }

    public function getEventsFrom($start = NULL, $end = NULL) {
        if ($start !== NULL || $end !== Null) {
            $dates = $this->fix_up_date($start, $end);
            $start = $dates['date1'];
            $end = $dates['date2'];
        }

        // Print the next 10 events on the user's calendar.
        $calendarId = 'primary';
        $optParams = array(
            'maxResults' => 10,
            'orderBy' => 'startTime',
            'singleEvents' => TRUE,
            'timeMin' => $start,
            'timeMax' => $end
        );
        $results = $this->calendar_service->events->listEvents($calendarId, $optParams);
        return $results;
    }

    private function fix_up_date ($date1 = NULL, $date2 = NULL){
        if ($date1 == NULL) {
            $date1 = date('c');
        } else {
            //checkdate ?? $date1
        }
        if ($date2 == NULL) {
            $date2 = "9999-02-12T15:19:21+00:00";
        } else {
            //checkdate $date2
        }
        return $dates = array (
                            'date1' = $date1,
                            'date2' = $date2
                        );
    }

    /**
     * Returns an authorized API client.
     * @return Google_Client the authorized client object
     */
    private function getClient() {
        session_start();

        $client = new Google_Client();
        $client->setAuthConfig('client_secrets.json');
        $client->addScope(Google_Service_Calendar::CALENDAR_READONLY);
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php');

        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $client->setAccessToken($_SESSION['access_token']);
            $this->calendar_service = new Google_Service_Calendar($client);
        } else {
            $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php';
            header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
        }
        ////???????????
        if (! isset($_GET['code'])) {
            $auth_url = $client->createAuthUrl();
            header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
        } else {
            $client->authenticate($_GET['code']);
            $_SESSION['access_token'] = $client->getAccessToken();
            $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/';
            header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
        }

        return $client;

        /*
        $client = new Google_Client();
        $client->setAuthConfig(CLIENT_SECRET_PATH);
        $client->addScope(Google_Service_Calendar::CALENDAR_READONLY);
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php');

        /*                        $client = new Google_Client();
                                $client->setApplicationName(APPLICATION_NAME);
                                $client->setScopes(SCOPES);
                                $client->setAuthConfig(CLIENT_SECRET_PATH);
        //                        $client->setAccessType('offline');

        $auth_url = $client->createAuthUrl();
        header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));

        $client->authenticate($_GET['code']);

        //          $access_token = $client->getAccessToken();

        // Load previously authorized credentials from a file.
        $credentialsPath = $this->expandHomeDirectory(CREDENTIALS_PATH);
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
        return $client;   *//
    }

    /**
     * Expands the home directory alias '~' to the full path.
     * @param string $path the path to expand.
     * @return string the expanded path.
     */
    function expandHomeDirectory($path) {
        $homeDirectory = getenv('HOME');
        if (empty($homeDirectory)) {
            $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
        }
        return str_replace('~', realpath($homeDirectory), $path);
    }
}
