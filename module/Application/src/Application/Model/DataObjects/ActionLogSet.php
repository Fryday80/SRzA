<?php
namespace Application\Model\DataObjects;


class ActionLogSet extends BasicDashboardDataSets {


function __construct($data)
{
    parent::__construct($data);
    $this->data = $this->sortNewToOld();
}

    public function toJSon($since = null)
    {
//        $this->data = $this->sortNewToOld();
        if ($since == null) return json_encode( $this->data );
        return json_encode( $this->getSince($since) );
    }

    public function toArray($since = null)
    {
//        $this->data = $this->sortNewToOld();
        if ($since == null) return $this->data;
        return $this->getSince($since);
    }

    public function getJSonUpdate($last_id, $last_timestamp)
    {
        $newData = $this->getSince($last_timestamp);
        /**
         * @var  $key
         * @var  $actionItem Action
         */
        foreach ( $newData as $key => $actionItem )
        {
            if ( ( $actionItem->id == $last_id ) )unset ( $newData[$key] );
        }
        return json_encode( $newData );
    }

    private function getSince($since)
    {
        $since = (is_object($since)) ? null : (int)$since;
        $newDataSet = array();
        $i = 0;
        while ( ( $i < count($this->data) ) && ( $this->data[$i]->time <= $since ) )
        {
            $newDataSet[$i] = $this->data[$i];
            $i++;
        }
        return $newDataSet;
    }

    private function sortNewToOld()
    {
        if(!isset($this->data[1]))return $this->data;
        if ( $this->data[0]->time < $this->data[ (count ( $this->data ) )-1 ]->time )return array_reverse($this->data);
        return $this->data;
    }

}