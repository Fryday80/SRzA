<?php
namespace Calendar\Service;

use DateTime;
use Exception;
use Google_Service_Calendar;
use Google_Client;
use Google_Service_Calendar_Event;

class CalendarService {
    private $APPLICATION_NAME;
    private $CREDENTIALS_PATH;
    private $CLIENT_SECRET_PATH;
    private $SCOPES;
    /** @var Google_Service_Calendar  */
    private $gCalendarService;

    function __construct($serviceManager) {
        $confPath = __DIR__.'/../../../config/';
        $this->APPLICATION_NAME = 'SRA Events';
        $this->CREDENTIALS_PATH = $confPath.'accessToken.json';
//        $this->CLIENT_SECRET_PATH = realpath($confPath.'client_secret.json');
        $this->CLIENT_SECRET_PATH = realpath($confPath.'client_secret.json');
        // If modifying these scopes, delete your previously saved credentials
        $this->SCOPES = implode(' ', array(
                Google_Service_Calendar::CALENDAR_READONLY)
        );
        // Get the API client and construct the service object.
        $client = $this->getClient();
        $this->gCalendarService = new Google_Service_Calendar($client);
    }
    public function getCalendars() {
        $data = $this->gCalendarService->calendarList->listCalendarList();
        bdump($data);
        return $data['modelData']['items'];
    }
    public function getEventsFrom($start, $end = null) {
        $start = new DateTime($start);
        $optParams = array(
            'maxResults' => 1,
            'orderBy' => 'startTime',
            'singleEvents' => TRUE,
            'timeMin' => $start->format('c'),
        );
        if($end) {
            $end = new DateTime($end);
            $optParams['timeMax'] = $end->format('c');
        }
        $items = array();
        $calendars = $this->getCalendars();
        foreach ($calendars as $calendar) {
            $results = $this->gCalendarService->events->listEvents($calendar['id'], $optParams);
            $items = array_merge($items, $results->getItems());
        }
        $result = [];
        /** @var Google_Service_Calendar_Event $value */
        foreach ($items as $value) {
            array_push($result, [
                'id'     => $value->getId(),
//                'calendarId'     => $value->getId(),
                'title'  => $value['summary'],
                'start'  => ($value['sequence'] == 3)? $value['start']['date'] : $value['start']['dateTime'],
                'end'    => ($value['sequence'] == 3)? $value['end']['date'] : $value['end']['dateTime'],
                'description' => $value['description'],
                'allDay' => ($value['sequence'] == 3)? true: false,
//                'url' => 'leer',
//                'className' => [''],
//                'editable' => false,
                'startEditable' => true,
                'durationEditable' => true,
//                'source' => null,
//                'color' => '',
//                'backgroundColor' => '',
//                'borderColor' => '',
//                'textColor' => '',
            ]);
        }
        return $result;
    }

    public function createEvent() {
        $calendarId = 'primary';
        $event = new Google_Service_Calendar_Event(array(
            'summary' => 'Google I/O 2015',
            'location' => '800 Howard St., San Francisco, CA 94103',
            'description' => 'A chance to hear more about Google\'s developer products.',
            'start' => array(
                'dateTime' => '2015-05-28T09:00:00-07:00',
                'timeZone' => 'America/Los_Angeles',
            ),
            'end' => array(
                'dateTime' => '2015-05-28T17:00:00-07:00',
                'timeZone' => 'America/Los_Angeles',
            ),
            'recurrence' => array(
                'RRULE:FREQ=DAILY;COUNT=2'
            ),
            'attendees' => array(
                array('email' => 'lpage@example.com'),
                array('email' => 'sbrin@example.com'),
            ),
            'reminders' => array(
                'useDefault' => FALSE,
                'overrides' => array(
                    array('method' => 'email', 'minutes' => 24 * 60),
                    array('method' => 'popup', 'minutes' => 10),
                ),
            ),
        ));
        $event = $this->gCalendarService->events->insert($calendarId, $event);
    }
    /**
     * Returns an authorized API client.
     * @return Google_Client the authorized client object
     * @throws Exception
     */
    function getClient() {
        $client = new Google_Client();
        $client->setApplicationName($this->APPLICATION_NAME);
        $client->setScopes($this->SCOPES);
        $client->setAuthConfig($this->CLIENT_SECRET_PATH);
        $client->setAccessType('offline');

        $client->setRedirectUri('http://localhost');
//        $client->setAccessType('offline');
        $client->setApprovalPrompt('force');

        // Load previously authorized credentials from a file.
        $credentialsPath = $this->expandHomeDirectory($this->CREDENTIALS_PATH);

        if (file_exists($credentialsPath)) {
            $accessToken = json_decode(file_get_contents($credentialsPath), true);
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            $authCode = trim('4/QnsgLTvzQ_WI2xIysHOF_ElkrINLhaX89YUckBtY5Cs');

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            // Store the credentials to disk.
            if(!file_exists(dirname($credentialsPath))) {
                mkdir(dirname($credentialsPath), 0700, true);
            }
            file_put_contents($credentialsPath, json_encode($accessToken));
//            printf("Credentials saved to %s\n", $credentialsPath);
        }
        $client->setAccessToken($accessToken);

        // Refresh the token if it's expired.
//        if ($client->isAccessTokenExpired()) {
//            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
//            bdump($accessToken);
//            bdump($client->getAccessToken());
//            file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
//        }// Refresh the token if it's expired.


        if ($client->isAccessTokenExpired()) {
            // save refresh token to some variable
            $refreshTokenSaved = $client->getRefreshToken();
            // update access token
            $client->fetchAccessTokenWithRefreshToken($refreshTokenSaved);
            // pass access token to some variable
            $accessTokenUpdated = $client->getAccessToken();
            // append refresh token
            $accessTokenUpdated['refresh_token'] = $refreshTokenSaved;
            // save to file
            file_put_contents($credentialsPath, json_encode($accessTokenUpdated));
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
        if (empty($homeDirectory)) {
            $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
        }
        return str_replace('~', realpath($homeDirectory), $path);
    }
}
