<?php
namespace Application\Service;

use Application\Model\Tables\SystemLogTable;
use Exception;
use Zend\Mvc\MvcEvent;
use Zend\Http\Response;
use Zend\Mvc\Application;
use Auth\Service\AccessService;
use Zend\Http\Header\SetCookie;
use Zarganwar\PerformancePanel\Register;
// data items
use Application\Model\DataModels\Stats;
use Application\Model\DataModels\Action;
use Application\Model\DataModels\ActiveUser;
use Application\Model\DataModels\SystemLog;
// enums
use Application\Model\Enums\HitType;
use Application\Model\Enums\LogType;
use Application\Model\Enums\OrderType;
use Application\Model\Enums\FilterType;
use Application\Model\Enums\ActionType;
use Application\Model\Enums\CounterType;

const STORAGE_PATH = '/storage/stats.log'; //relative to root, start with /
const AJAX_BLACK_LIST = array(
    '/',
    '/system/json',
    '/system/dashboard'
);

class StatisticService
{
    private $storagePath;
    /** @var Stats $storage */
    private $stats;
    /** @var  AccessService */
    private $accessService;
    /** @var  SystemLogTable */
    private $sysLog;

    private $timeLogger = true;

    public function __construct(AccessService $accessService, SystemLogTable $sysLogTable) {
        $this->accessService = $accessService;
        $this->sysLog = $sysLogTable;
        $this->storagePath = getcwd().STORAGE_PATH;
        $this->stats = (file_exists($this->storagePath)) ? $this->loadFile() : new Stats();
    }
    
//======================================================================================================= EVENTS
    public function onDispatch(MvcEvent $e)
    {
        $data = $this->gatherData($e);
        // skip on Ajax requests
        if($data['request']->isXmlHttpRequest() && in_array($data['url'], AJAX_BLACK_LIST)) return;

        $this->stats->logAction(new Action($data['mTime'], $data['url'], $data['userId'], $data['userName'], ActionType::PAGE_CALL , 'Call', $data['url']));
        $this->stats->updateActiveUser( new ActiveUser($data['userId'], $data['userName'], $data['mTime'], $data['ip'], $data['url']) , $data['sid']);
        $this->stats->logPageHit(($this->accessService->hasIdentity())? HitType::MEMBER : HitType::GUEST, $data['url'], $data['mTime']);

        $this->checkCookie($e);
    }

    public function onError(MvcEvent $e) {
		if ($this->timeLogger) Register::add('Error Handling onError - start');
        $error = $e->getError();
        if (empty($error)) {
            return;
        }

        // Do nothing if the result is a response object
        $result = $e->getResult();
        if ($result instanceof Response) {
            return;
        }

        switch ($error) {
            case Application::ERROR_CONTROLLER_NOT_FOUND:
            case Application::ERROR_CONTROLLER_INVALID:
            case Application::ERROR_ROUTER_NO_MATCH:
                // Specifically not handling these
                //here 404 missmatch is prozessed
                return;

            case Application::ERROR_EXCEPTION:
            default:
                /** @var Exception $exception */
                $exception = $e->getParam('exception');

                $errors = [];
                do {
                    array_push($errors, array(
                        'name' => get_class($exception),
                        'file' => $exception->getFile(),
                        'line' => $exception->getLine(),
                        'msg'  => $exception->getMessage(),
                        'stackTrace' => $exception->getTraceAsString()
                    ));
                } while($exception = $exception->getPrevious());
                bdump($errors);
                break;
        }
        
        $data = $this->gatherData($e);
        $data['errors'] = $errors;

        $this->stats->logAction(new Action($data['mTime'], $data['url'], $data['userId'], $data['userName'], ActionType::ERROR , 'Call', $data['url']) );
        $this->stats->logPageHit($data['hitType'], $data['url'], $data['mTime']);
        $this->logSystem( new SystemLog($data['mTime'], $data['logType'], $data['errors'][0]['msg'], $data['url'], $data['userId'], $data['userName'], $data['data'] ));
		if ($this->timeLogger) Register::add('Error Handling onError - end');
    }

    public function onFinish(MvcEvent $e) {
        $this->saveFile($this->stats);
    }

//======================================================================================================= LOGGING
    public function logSystem(SystemLog $log){
    	bdump($log);
		if ($this->timeLogger) Register::add('Syslog start');
        $this->sysLog->updateSystemLog($log);
		bdump($log);
    }

    public function logAction(Action $action)
    {
        $this->stats->logAction($action);
    }

//======================================================================================================= PUBLIC GET
    /**
     * @param int $count CounterType::XX
     * @return mixed
     */
    public function getPageHits ($count = CounterType::ALL){
        return $this->stats->getPageHits($count);
    }

    /**
     * @param int $since
     * @return array|null
     */
    public function getActiveUsers($since = 0){
        return $this->stats->getActiveUsers($since);
    }

    /** Checks if user is active
     * @param $userName
     * @return bool
     */
    public function isActive($userName){
        $activeUsers = $this->getActiveUsers();
        foreach ($activeUsers as $user){
            if ($user->userName == $userName) return true;
        }
        return false;
    }
    /**
     * @param int $since
     * @return array
     */
    public function getSystemLog ($since = 0){
        return $this->getSystemLogWhere(array('since' => $since));
    }

    /**
     * @param array $where array("key" => "value") ... "since" => timestamp also possible
     * @param array $options arrayKeys: <br>filterType => FilterType::XX , <br>sortKey, <br>sortOrder => OrderType::XX
     * @return array array of results
     */
    public function getSystemLogWhere($where = null, $options = array("filterType" => FilterType::EQUAL, "sortKey" => "time", "sortOrder" => OrderType::DESCENDING))
    {
        $data = $this->sysLog->getSystemLogs();
        // just fetch all
        if (!is_array($where))
            return $this->stats->sortByKey($data, $options['sortKey'], $options['sortOrder']);
        // fetch since if only since is given
        if (key_exists('since', $where) && count($where) == 1)
            return $this->stats->getSinceOf($data, $where['since']);
        foreach ($where as $sKey => $sValue){
            if ($sKey == 'since'){
                $data = $this->stats->getSinceOf($data, $where['since']);
            } else {
                $data = $this->stats->filterByKey($data, $sKey, $sValue, $options['filterType']);
            }
        }
        return $this->stats->sortByKey($data, $options['sortKey'], $options['sortOrder']);

    }

    /**
     * @param int $since timestamp microtime()*1000
     * @return array|mixed
     */
    public function getActionLog($since = 0){
        return $this->stats->getActionLog($since);
    }

    /**
     * @param int $top number of top entries
     * @return array result array
     */
    public function getMostVisitedPages($top = 1){
        return $this->stats->getMostVisitedPages($top);
    }

//======================================================================================================= PRIVATES

    /**
     * microtime is passed through to data object via <b>$data['mTime']</b><br>
     * and processed there so the different logs have one common value and fitting timestamps<br>
     * <code> $mTime*10000</code> as <b> id </b>
     * and <br>
     * <code>(int)$mTime </code> as (UNIX-)<b>timestamp</b>
     *
     * @param $e
     * @return array
     */
    private function gatherData($e)
    {
        $data = array(
            'mTime' => microtime(true),
            'request' => $e->getApplication()->getRequest(),
            'sid' => $this->accessService->session->getManager()->getId(),
            'userName' => $this->accessService->getUserName(),
            'hitType'  => ( $this->accessService->hasIdentity() )? HitType::MEMBER : HitType::GUEST,
        );

        $serverData = $data['request']->getServer()->toArray();
        $data['url']      = $serverData['REQUEST_URI'];
        $data['ip']       = $serverData['REMOTE_ADDR'];
        $data['userId']   = $this->accessService->getUserID();
        $data['userId']   = ( $data['userId'] == "-1" ) ? $this->stats->getActiveGuestId($data['sid']) : $data['userId'];
        $data['userName'] = ( $data['userId'] > $this->stats->guestNumbersMin ) ? "Guest" : $data['userName'];
        $data['logType']  = ( $data['hitType'] == HitType::MEMBER ) ? LogType::ERROR_MEMBER : LogType::ERROR_GUEST;
        $data['data']['serverPHPData'] = $serverData;

        // not used -- prepared for redirect logging
        if (isset ($serverData['HTTP_REFERER']) ) {
            //@enhancement log if referred
            // prepared if referring data is needed
//        $data['replace']= array( "http://", $data['serverPHPData']['HTTP_HOST'] );
//        $data['referrer']= (isset ($data['serverPHPData']['HTTP_REFERER']) ) ? $data['serverPHPData']['HTTP_REFERER'] : "direct call";
//        $data['relativeReferrerURL']= str_replace( $data['replace'],"", $data['referrer'], $counter );
//        $data['redirect']= (isset ($data['serverPHPData']['REDIRECT_STATUS'])) ? $data['serverPHPData']['REDIRECT_STATUS'] : "no redirect"; //set if redirected
//        $data['redirectedTo']= (isset ($data['serverPHPData']['REDIRECT_URL']) ) ? $data['serverPHPData']['REDIRECT_URL'] : "no redirect";
        }
        return $data;
    }
    
    private function checkCookie(MvcEvent $e) {
        if (!$e->getRequest()->getCookie() || !$e->getRequest()->getCookie()->offsetExists('srzaiknowyou')) {
            $this->stats->logNewUser();
            $cookie = new SetCookie('srzaiknowyou', time(), time() + 9999999);
            $e->getResponse()->getHeaders()->addHeader($cookie);
            $this->getPageHits(0);
        }
    }

    private function saveFile($content) {
        $content = serialize($content);
        file_put_contents($this->storagePath, $content);
        return true;
    }

    private function loadFile() {
        $content = file_get_contents($this->storagePath);
        $content = unserialize($content);
        return $content;
    }
}