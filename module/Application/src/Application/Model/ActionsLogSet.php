<?php
namespace Application\Model;

use Application\Model\BasicModels\StatDataSetBasic;
use Application\Utility\CircularBuffer;

class ActionsLogSet
    extends StatDataSetBasic
{
    /**** CIRCULAR BUFFER ****/
    const ACTIONS_CACHE_NAME = 'stats/actions';
    /** @var  $buffer CircularBuffer */
    private $buffer;

    function __construct()
    {
        $this->buffer = new CircularBuffer(100);
    }

    public function updateItem($data) {
        /** @var  $action ActionsLog */
        $action = $this->create($data);
        if ( $action !== null ) $this->buffer->push($action);
        $this->sortResult();
    }

    public function toArray($since = null)
    {
        if ($since == null) return $this->data;
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

    private function create($data)
    {
        if (!$data) return NULL;
        $url = $time = $userId = $type = $title = $msg = $itemData = false;
        foreach ($data as $key => $value){
            $url      = ( key_exists('url',$data) )    ? $data['url']    : false;
            $time     = ( key_exists('time',$data) )   ? $data['time']   : false;
            $userId   = ( key_exists('userId',$data) ) ? $data['userId'] : false;
            $itemData = ( key_exists('data',$data) )   ? $data['data']   : null;
        }
        if (!($url && $time && $userId && $type && $title && $msg))return null;
        return new ActionsLog( uniqid(), $url, $time, $userId, $type, $title, $msg,  $itemData);
    }
    private function sortResult(){
        return $this->data = array_reverse( $this->buffer->toArray() );
    }
}