<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 13.04.2017
 * Time: 02:13
 */

namespace Application\Service;

use Application\Model\Abstracts\ActionType;
use Application\Model\Abstracts\CounterType;
use Application\Model\Abstracts\FilterType;
use Application\Model\Abstracts\HitType;
use Application\Model\Abstracts\LogType;
use Application\Model\Abstracts\OrderType;
use Application\Model\Action;
use Application\Model\ActiveUser;
use Application\Model\SystemLogTable;
use Application\Model\Stats;
use Application\Model\SystemLog;
use Auth\Service\AccessService;
use Exception;
use Zarganwar\PerformancePanel\Register;
use Zend\Http\Header\SetCookie;
use Zend\Http\Response;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;

const STORAGE_PATH = '/storage/stats.log'; //relative to root, start with /
const AJAX_BLACK_LIST = array(
    '/',
    '/system/json',
    '/system/dashboard'
);
/** "true" logs speed in Tracy "false" don't */
const SPEED_CHECK = true;

class StatisticService
{
    private $storagePath;
    /** @var Stats $storage */
    private $stats;
    /** @var  AccessService */
    private $accessService;
    /** @var  SystemLogTable */
    private $sysLog;

    private $isError = false;
    private $errorData = array();

    function __construct($sm)
    {
        if (SPEED_CHECK) Register::add('StatService start');
        $this->accessService = $sm->get('AccessService');
        $this->sysLog = $sm->get('Application\Model\SystemLog');
        $this->storagePath = getcwd().STORAGE_PATH;
        $this->stats = (file_exists($this->storagePath)) ? $this->loadFile() : new Stats();
        if (SPEED_CHECK) Register::add('StatService constructed');
    }
//======================================================================================================= EVENTS
    public function onDispatch(MvcEvent $e)
    {
        $data = $this->gatherData($e);
        // skip on Ajax requests
        if($data['request']->isXmlHttpRequest() && in_array($data['url'], AJAX_BLACK_LIST)) return;

        $this->stats->logAction(new Action($data['mTime'], $data['url'], $data['userId'], $data['userName'], ActionType::PAGE_CALL , 'Call', $data['url']));
        $this->stats->logPageHit(($this->accessService->hasIdentity())? HitType::MEMBER : HitType::GUEST, $data['url'], $data['mTime']);
        $this->stats->updateActiveUser( new ActiveUser($data['userId'], $data['userName'], $data['mTime'], $data['ip'], $data['url']) , $data['sid']);

        $this->checkCookie($e);
    }

    public function onError(MvcEvent $e) {

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
                        'msg' => $exception->getMessage(),
                        'stackTrace' => $exception->getTraceAsString()
                    ));
                } while($exception = $exception->getPrevious());
                bdump($errors);
                break;
        }

        $this->isError = true;
        $this->errorData = $this->gatherData($e);
    }

    public function onFinish(MvcEvent $e) {
        if ($this->isError){
            $this->isError = false;
            $this->errorData = $this->gatherErrorData($e, $this->errorData);
            $this->errorLog($this->errorData);
            // salt @todo for security $e->setViewModel(new ErrorPage_without $data);
        }
        $this->saveFile($this->stats);
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
        if (SPEED_CHECK) Register::add('StatService get SysLog start');
        $data = $this->stats->systemLog;
        //@todo re-build to db
//        $data = $this->sysLog->getSystemLogs();
        if (SPEED_CHECK) Register::add('StatService get SysLog db/var fetched');
        // just fetch all
        if (!is_array($where)) {
            if (SPEED_CHECK) Register::add('StatService get SysLog return & end');
            return $this->stats->sortByKey($data, $options['sortKey'], $options['sortOrder']);
        }
        // fetch since if only since is given
        if (key_exists('since', $where) && count($where) == 1){
            if (SPEED_CHECK) Register::add('StatService get SysLog return & end');
            return $this->stats->getSinceOf($data, $where['since']);
        }
        foreach ($where as $sKey => $sValue){
            if ($sKey == 'since'){
                $data = $this->stats->getSinceOf($data, $where['since']);
            } else {
                $data = $this->stats->filterByKey($data, $sKey, $sValue, $options['filterType']);
            }
        }
        if (SPEED_CHECK) Register::add('StatService get SysLog return & end');
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
    // could be made public as for logging a set of data
    private function errorLog($data){
        $this->stats->logAction(new Action($data['mTime'], $data['url'], $data['userId'], $data['userName'], ActionType::ERROR , 'Call', $data['url']) );
        $this->stats->logPageHit($data['hitType'], $data['url'], $data['mTime']);
        $this->stats->logSystem( new SystemLog($data['mTime'], $data['logType'], $data['msg'], $data['url'], $data['userId'], $data['userName'], $data['data'] ));
        unset($this->errorData);
    }

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
        if (SPEED_CHECK) Register::add('StatService ->gatherData start');
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
            // prepared if referring data is needed
//        $data['replace']= array( "http://", $data['serverPHPData']['HTTP_HOST'] );
//        $data['referrer']= (isset ($data['serverPHPData']['HTTP_REFERER']) ) ? $data['serverPHPData']['HTTP_REFERER'] : "direct call";
//        $data['relativeReferrerURL']= str_replace( $data['replace'],"", $data['referrer'], $counter );
//        $data['redirect']= (isset ($data['serverPHPData']['REDIRECT_STATUS'])) ? $data['serverPHPData']['REDIRECT_STATUS'] : "no redirect"; //set if redirected
//        $data['redirectedTo']= (isset ($data['serverPHPData']['REDIRECT_URL']) ) ? $data['serverPHPData']['REDIRECT_URL'] : "no redirect";
        }
        if (SPEED_CHECK) Register::add('StatService ->gatherData end');
        return $data;
    }

    private function gatherErrorData(MvcEvent $e, $data)
    {
//        todo get the translator
        $error = $e->getViewModel()->getChildren()[0];
        
        if (isset($error->display_exceptions) && $error->display_exceptions) {
            if (isset($error->exception) && $error->exception instanceof Exception) {
                $errorData['error'][$this->translate('Additional information')] = get_class($error->exception);
                $errorData['error'][$this->translate('File')] = $error->exception->getFile();
                $errorData['error']['line'] = $error->exception->getLine();
                $data['msg'] = $errorData['error'][$this->translate('Message')] = $error->exception->getMessage();
                $errorData['error'][$this->translate('Stack trace')] = $error->exception->getTraceAsString();

                $e = $error->exception->getPrevious();
                if ($e) {
                    $errorData['error'][$this->translate('Previous exceptions')] = array();
                    while ($e) {
                        $dataSet = array();
                        $dataSet['class_info'] = get_class($e);
                        $dataSet[$this->translate('File')] = $e->getFile();
                        $dataSet['line'] = $e->getLine();
                        $dataSet[$this->translate('Message')] = $e->getMessage();
                        $dataSet[$this->translate('Stack trace')] = $e->getTraceAsString();

                        array_push($errorData['error'][$this->translate('Previous exceptions')], $dataSet);
                        $e = $e->getPrevious();
                    }
                } else {
                    $errorData['error'][$this->translate('Previous exceptions')] = $this->translate('No Exception available');
                }
                $data['data']['errorPage'] = $errorData;
            }
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
        if (SPEED_CHECK) Register::add('load and unserialize');
        $content = file_get_contents($this->storagePath);
        $content = unserialize($content);
        if (SPEED_CHECK) Register::add('load and unserialize end');
        return $content;
    }

    //mocking function
    private function translate($string){
        return $string;
    }
}