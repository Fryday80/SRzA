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

    function __construct()
    {
        $this->buffer = new CircularBuffer(100);
    }

    /**
     * @param $type string
     * @param $title string
     * @param $msg string
     * @param $data mixed (serializable)
     */
    public function updateActionsLog($type, $title, $msg, $data = null) {
        /** @var  $action ActionsLog */
        $action = new ActionsLog( $type, $title, $msg, time(), $this->userId(), $data );
        $this->buffer->push($action);
        $this->result();
    }

    public function toArray($since = null)
    {
        if ($since == null) return $this->actionsLogSet;
        return $this->getSince($since);
    }

    public function getByIDAndTime($last_id, $last_timestamp)
    {
        $newData = $this->getSince($last_timestamp);
        foreach ( $newData as $key => $actionItem )
        {
            if ( ( $actionItem->actionID == $last_id ) )unset ( $newData[$key] );
        }
        return $newData;
    }

    /**** PRIVATE METHODS ****/
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

    private function result(){
        return $this->actionsLogSet = array_reverse( $this->buffer->toArray() );
    }
}