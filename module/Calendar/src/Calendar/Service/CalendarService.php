<?php
namespace Calendar\Service;

use Application\Service\CacheService;
use DateTime;
use DoctrineORMModuleTest\Assets\Entity\Date;
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
    /** @var  CacheService */
    private $cacheService;
    private $calendarOverwrites;
    private $minCacheDate;
    private $maxCacheDate;


    function __construct($serviceManager) {
        $confPath = __DIR__.'/../../../config/';
        $this->APPLICATION_NAME = 'SRA Events';
        $this->CREDENTIALS_PATH = $confPath.'accessToken.json';
        $this->CLIENT_SECRET_PATH = realpath($confPath.'client_secret.json');
        //load cached events
        $this->cacheService = $serviceManager->get('CacheService');
        $this->minCacheDate = date("Y-m-d", mktime(0, 0, 0, date("m") - 10, date("d"), date("Y")));
        $this->maxCacheDate = date("Y-m-d", mktime(0, 0, 0, date("m") + 10, date("d"), date("Y")));
    }


    function sort($a, $b) {
        if ($a['startUnix'] == $b['startUnix']) {
            return 0;
        }
        return ($a['startUnix'] < $b['startUnix']) ? -1 : 1;
    }
    public function getUpcoming($count = 5) {
        $maxMonth = 5;
        $now = date("Y-m-d", time());
        $end = date("Y-m-d", mktime(0, 0, 0, date("m") + $maxMonth, date("d"), date("Y")));
        $events = $this->getEventsFrom($now, $end);
        usort($events, array($this, 'sort'));
        return array_slice($events, 0, $count);
    }
    /**
     * @param $calendarID
     * @return array
     */
    public function getCalendarOverwrites($calendarID = null) {
        $overwrites = $this->cacheService->getCache('calendar/overwrites');
        if ($calendarID === null) {
            //return the overwrites for all calendars
            return $overwrites;
        } else {
            if (isset($overwrites[$calendarID])) return $overwrites[$calendarID];
        }
        return array();
    }

    /**
     * @param $data
     * @return bool
     */
    public function setCalendarOverwrites($data) {
        if (!isset($data['id'])) return false;
        $overwrites = $this->cacheService->getCache('calendar/overwrites');
        if(!isset($overwrites[$data['id']])) {
            $overwrites[$data['id']] = array();
        }
        if (isset($data['active'])) $overwrites[$data['id']]['active'] = $data['active'];
        if (isset($data['summary'])) $overwrites[$data['id']]['summary'] = $data['summary'];
        if (isset($data['backgroundColor'])) $overwrites[$data['id']]['backgroundColor'] = $data['backgroundColor'];
        if (isset($data['textColor'])) $overwrites[$data['id']]['textColor'] = $data['textColor'];
        if (isset($data['borderColor'])) $overwrites[$data['id']]['borderColor'] = $data['borderColor'];
        if (isset($data['roleId'])) $overwrites[$data['id']]['roleId'] = $data['roleId'];

        $this->cacheService->setCache('calendar/overwrites', $overwrites);
    }

    public function getCalendars($reload = false) {
        if ($reload || !$this->cacheService->hasCache('calendar/calendars')) {
            $this->cacheService->setCache('calendar/calendars', $this->gLoadCalendars());
        }
        $rawData = $this->cacheService->getCache('calendar/calendars');
        $calendars = [];
        //map calendar config
        foreach ($rawData as $item) {
            $overwrite = $this->getCalendarOverwrites($item['id']);
            array_push($calendars, array(
                'id' => $item['id'],
                'summary' => $item['summary'],
                'description' => (isset($item['description']))? $item['description']: '',
                'backgroundColor' => (isset($overwrite['backgroundColor']))? $overwrite['backgroundColor']: $item['backgroundColor'],
                'textColor' => (isset($overwrite['textColor']))? $overwrite['textColor']: $item['foregroundColor'],
                'borderColor' => (isset($overwrite['borderColor']))? $overwrite['borderColor']:
                    (isset($overwrite['backgroundColor']))? $overwrite['backgroundColor']: $item['backgroundColor'],
                'roleId' => (isset($overwrite['roleId']))? $overwrite['roleId']: 1,
                'active' => (isset($overwrite['active']))? $overwrite['active']: false,
            ));
        }
        return $calendars;
//        "de.german#holiday@group.v.calendar.google.com"
    }
    public function getEventsFrom($start, $end = null) {
        $filtered = [];
        if (strtotime($start) > strtotime($this->maxCacheDate) || strtotime($end) < strtotime($this->minCacheDate)) {
            //out of cache bounds -> load events from google
            $events = $this->gGetEventsFrom($this->minCacheDate, $this->maxCacheDate);
            foreach ($events as $event) {
                //@todo check rights
                array_push($filtered, $event);
            }
        } else {
            if (!$this->cacheService->hasCache('calendar/events')) {
                $events = $this->cacheEvents();
            } else {
                $events = $this->cacheService->getCache('calendar/events');
            }
            foreach ($events as $event) {
                //@todo check rights
                if (strtotime($event['start']) > strtotime($end) || strtotime($event['end']) < strtotime($start)) continue;
                array_push($filtered, $event);
            }
            return $filtered;
        }
        return $events;
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











    private function cacheEvents() {
        $events = $this->gGetEventsFrom($this->minCacheDate, $this->maxCacheDate);
        $this->cacheService->setCache('calendar/events', $events);
        return $events;
    }
    private function gGetEventsFrom($start, $end = null) {
        $start = new DateTime($start);
        $optParams = array(
            'maxResults' => 50,
            'singleEvents' => false,
            'timeMin' => $start->format('c'),
        );
        if($end) {
            $end = new DateTime($end);
            $optParams['timeMax'] = $end->format('c');
        }
        $result = [];
        $calendars = $this->getCalendars();
        $this->gGetCalendarService();
        foreach ($calendars as $calendar) {
            $overwrites = $this->getCalendarOverwrites($calendar['id']);
            if (!$calendar['active']) continue;
            $events = $this->gCalendarService->events->listEvents($calendar['id'], $optParams)->getItems();
            /** @var Google_Service_Calendar_Event $value */
            foreach ($events as $event) {
                $start = (isset($event['start']['dateTime']))? $event['start']['dateTime'] : $event['start']['date'];
                $end = (isset($event['end']['dateTime']))? $event['end']['dateTime'] : $event['end']['date'];
                array_push($result, [
                    'id'     => uniqid(),
                    'gId'     => $event->getId(),
//                'calendarId'     => $value->getId(),
                    'title'  => $event['summary'],
                    'start'  => $start,
                    'end'    => $end,
                    'startUnix' => (new DateTime($start))->getTimestamp(),
                    'endUnix' => (new DateTime($end))->getTimestamp(),
                    'description' => $event['description'],
                    'allDay' => ($event['sequence'] == 3)? true: false,
//                'url' => 'leer',
//                'className' => [''],
//                'editable' => false,
                    'startEditable' => true,
                    'durationEditable' => true,
//                'source' => null,
//                'color' => (isset($overwrites['color']))? $overwrites['color'] : $calendar['backgroundColor'],
                'textColor' => (isset($overwrites['textColor']))? $overwrites['textColor'] : $calendar['textColor'],
                'backgroundColor' => (isset($overwrites['backgroundColor']))? $overwrites['backgroundColor'] : $calendar['backgroundColor'],
                'borderColor' => (isset($overwrites['borderColor']) && $overwrites['borderColor'] != '')? $overwrites['borderColor'] : 'rgba(0,0,0,0)',


                //recurringEventId  // id of the original event

                ]);
            }
        }
        return $result;
    }




    private function gLoadCalendars() {
        $calendars = array();
        $this->gGetCalendarService();
        $data = $this->gCalendarService->calendarList->listCalendarList();
        foreach ($data['modelData']['items'] as $calendar) {
            $item = array(
                'id' => $calendar['id']
            );
//                etag => ""1487926110679000"" (18)
//                timeZone => "Europe/Berlin" (13)
//                colorId => "3"
//                selected => TRUE
//                accessRole => "owner" (5)
//                defaultReminders => array ()
            if (isset($calendar['summary'])) $item['summary'] = $calendar['summary'];
            if (isset($calendar['backgroundColor'])) $item['backgroundColor'] = $calendar['backgroundColor'];
            if (isset($calendar['foregroundColor'])) $item['foregroundColor'] = $calendar['foregroundColor'];
            if (isset($calendar['description'])) $item['description'] = $calendar['description'];
            array_push($calendars, $item);
        }
        return $calendars;
    }
    private function gGetCalendarService() {
        if (!$this->gCalendarService)
            $this->gInitCalendarService();
        return $this->gCalendarService;
    }
    private function gInitCalendarService() {
        // If modifying these scopes, delete your previously saved credentials
        $this->SCOPES = implode(' ', array(
                Google_Service_Calendar::CALENDAR_READONLY)
        );
        // Get the API client and construct the service object.
        $client = $this->gGetClient();
        $this->gCalendarService = new Google_Service_Calendar($client);
    }
    /**
     * Returns an authorized API client.
     * @return Google_Client the authorized client object
     * @throws Exception
     */
    private function gGetClient() {
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
    private function expandHomeDirectory($path) {
        $homeDirectory = getenv('HOME');
        if (empty($homeDirectory)) {
            $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
        }
        return str_replace('~', realpath($homeDirectory), $path);
    }

}
