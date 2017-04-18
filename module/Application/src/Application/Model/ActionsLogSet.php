<?php
namespace Application\Model;

use Application\Utility\CircularBuffer;

class ActionsLogSet
    extends BasicStatDataSet
{
    /**** CIRCULAR BUFFER ****/
    const ACTIONS_CACHE_NAME = 'stats/actions';
    private static $instance;
    /** @var  $cache CacheService */
    private $cache;
    
    /** @var  $buffer CircularBuffer */
    private $buffer;
    private $actionsLogSet = array(); // updated and sorted @ update

    function __construct($accessService, $sm)
    {
        parent::__construct($accessService);

        /**** CACHE ****/
        $this->cache = $sm->get('CacheService');
        /**** CIRCULAR BUFFER - actionsLog DATA SET****/
        self::$instance = $this;
        if (!$this->cache->hasCache($this::ACTIONS_CACHE_NAME)) {
            $this->buffer = new CircularBuffer(100);
            $this->cache->setCache($this::ACTIONS_CACHE_NAME, $this->buffer);
        } else {
            $this->buffer = $this->cache->getCache($this::ACTIONS_CACHE_NAME);
        }
    }

    /**
     * @param $type string
     * @param $title string
     * @param $msg string
     * @param $data mixed (serializable)
     */
    public function updateActionsLog($type, $title, $msg, $data = null) {
        /** @var  $action ActionsLog */
        $action = new ActionsLog( $type, $title, $msg, time(), $this->getUserId(), $data );
        $this->buffer->push($action);
        $this->result();
    }
    
    public function toJSon($since = null)
    {
        if ($since == null) return json_encode( $this->actionsLogSet );
        return json_encode( $this->getSince($since) );
    }

    public function toArray($since = null)
    {
        if ($since == null) return $this->actionsLogSet;
        return $this->getSince($since);
    }

    public function getJSonUpdate($last_id, $last_timestamp)
    {
        $newData = $this->getSince($last_timestamp);
        /**
         * @var  $key
         * @var  $actionItem ActionsLog
         */
        foreach ( $newData as $key => $actionItem )
        {
            if ( ( $actionItem->actionID == $last_id ) )unset ( $newData[$key] );
        }
        return json_encode( $newData );
    }

    /**** PRIVATE METHODS ****/

    private function result(){
        bdump($this->buffer->toArray());
        $this->actionsLogSet = $this->buffer->toArray();
    }
    /**
     * @param $since int
     * @return array
     */
    private function getSince($since)
    {
        $since = (is_object($since)) ? 0 : (int)$since;
        $newDataSet = array();
        $i = 0;
        while ( ( $i < count($this->actionsLogSet) ) && ( $this->actionsLogSet[$i]->time >= $since ) )
        {
            $newDataSet[$i] = $this->actionsLogSet[$i];
            $i++;
        }
        return $newDataSet;
    }
}