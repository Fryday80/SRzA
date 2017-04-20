<?php
namespace Application\Model;


use Application\Model\BasicModels\StatsCollectionBasic;

class StatisticDataCollection
    extends StatsCollectionBasic
{
//    /** @var $pageHits pageHitsSet */
//    private $pageHitsSet;
    /** @var  $activeUsers ActiveUsersSet */
    private $activeUsersSet;
//    /** @var  $actionsLogSet actionsLogSet */
//    private $actionsLogSet;
    /** @var $systemLogSet SystemLogSet  */
    private $systemLogSet;
    
    function __construct()
    {
        parent::__construct(); // will be overridden but otherwise the IDE claims it missing
        /**** DATA SETS ****/
//        $this->pageHitsSet    =
            $sets['pageHits']    =
                new PageHitsSet();
        $this->activeUsersSet =
            $sets['activeUsers'] =
                new ActiveUsersSet();
//        $this->actionsLogSet  =
            $sets['actionsLog'] =
                new actionsLogSet();
        $this->systemLogSet   =
//            $sets['sysLogSet'] =
                new SystemLogSet();
        foreach($sets as $key => $set)
            $this->realData[$key] = array(
                "SET" => $set,
                "hash" => $set->getHashTables()
        );
    }

    /**** UPDATE ****/
    /**** PAGE HITS ****/
    public function updatePageHit($data){
        $this->updateItemOf('pageHits', $data);
    }
    /**** ACTIONS LOG ****/
    public function updateActionsLog( $data ){
        $this->updateItemOf( 'actionsLog', $data );
    }
    /**** ACTIVE USERS ****/
    public function updateActiveUsers( $data ){
        $this->updateItemOf( 'activeUsers', $data );
    }


    private function updateItemOf($setName, $data){
        $this->fetchDataSet($setName);
        $this->activeSet->updateItem($data);
    }
    
    
    /**** SET ****/
    /**** SYS LOG ****/
    public function updateSystemLog($type, $msg, $data = null){
        $this->systemLogSet->updateSystemLog($type, $msg, $data);
    }
    /**** GET ****/
    /**** ACTIVE USERS ****/
    public function getActiveUsers(){
        return $this->activeUsersSet->toArray();
    }
    public function getActiveGuests(){
        return $this->activeUsersSet->getActiveGuests();
    }
    public function getGuestCount(){
        return $this->activeUsersSet->getGuestCount();
    }
    /**** ACTIONS LOG ****/
    public function getActionsLog($since = null){
        return $this->actionsLogSet->toArray($since);
    }
    public function getActionsLogByIDAndTime($last_id, $last_timestamp){
        return $this->actionsLogSet->getByIDAndTime($last_id, $last_timestamp);
    }
    /**** PAGE HITS ****/
    public function getPageHits($since = null){
        return $this->pageHitsSet->toArray($since);
    }

    public function getByUrl($url){
        return $this->pageHitsSet->getByUrl($url);
    }

    public function getHitsByUrl($url){
        return $this->pageHitsSet->getHitsByUrl($url);
    }

    public function getAllHits(){
        return $this->pageHitsSet->getAllHits();
    }
    public function getMostVisitedPages($top = 1){
        return $this->pageHitsSet->getMostVisitedPages($top);
    }
    /**** SYS LOG ****/
    public function getSysLog($since = null){
        return $this->systemLogSet->toArray($since);
    }
    public function getSystemLogByType ($type, $since = null){
        return $this->systemLogSet->getSystemLogByType ($type, $since);
    }
    public function getSystemLogByUser ($userId, $since = null){
        return $this->systemLogSet->getSystemLogByUser ($userId, $since);
    }
    public function getNumberOfLogs(){
        return $this->systemLogSet->getNumberOfLogs();
    }

    /**
     * @param string $dataSet actionslog | activeusers | pagehits | syslog
     * @param $key
     * @param string $sort desc | asc
     * @param int $limit max of results
     * @return array|bool
     */
    private function getSortedDataOfByItemKey($dataSet, $key, $sort = 'desc', $limit = 0){
        $sorting = ($sort == 'desc') ? 'desc' : 'asc';
        if (strtolower($dataSet) == 'actionslog')  $usedSet = $this->actionsLogSet->toArray();
        if (strtolower($dataSet) == 'activeusers') $usedSet = $this->activeUsersSet->toArray();
        if (strtolower($dataSet) == 'pagehits')    $usedSet = $this->pageHitsSet->toArray();
        if (strtolower($dataSet) == 'syslog')      $usedSet = $this->systemLogSet->toArray();
        if (! isset($usedSet)) return null;
        if (! isset($usedSet[0]->$key)) {bdump('collection, get sorted key not exists'); return null;}
        else
        {
            $result = array();
            $list = array();
            $count = 1;
            foreach ($usedSet as $item) {
                if (!isset($list[$item->$key])) $list[$item->$key] = array();
                array_push($list[$item->$key], $item);
            }
            if ($sorting == 'desc') krsort($list);
            else ksort($list);
            foreach ($list as $item) {
                array_push($result, $item);
                $count++;
                if ($count == (int)$limit) return $result;
            }
            if ((count($result) < $limit) && (count($result) >= 1)) return $result;
        }
        return false;
    }
    private function getMostVisitedPagesIIIIII($top = 1){
        $result = array();
        $list = array();
        $count = 0;
        /** @var  $item Page*/
        foreach ($this->data as $item){
            if (!isset($list[$item->count])) $list[$item->count] = array();
            array_push($list[$item->count], $item->url);
        }
        krsort($list);
        foreach($list as $hits => $item)
            foreach ($item as $url){
                if($count == (int)$top) return $result;
                array_push($result, array('hits' => $hits, 'url' => $url));
                $count++;
            }
        if ((count($result) < $top) && (count($result) >= 1))return $result;
        return false;
    }
    private function isDataSet($set){
        if (! isset ($this->$set))return null;
    }
    
    
}