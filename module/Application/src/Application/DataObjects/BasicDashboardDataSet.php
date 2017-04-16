<?php

namespace Application\DataObjects;


use Zend\Db\ResultSet\ResultSet;

class BasicDashboardDataSet
{
    protected $since;

    public $data;

    function __construct($data = null, $since = null)
    {
        if ($data !== null) {
            $this->data = (($data instanceof ResultSet)) ? $data->toArray() : $data;
            $this->decode('data');
        }
        $this->setSince($since);
    }

    public function toArray($since = null)
    {
        $this->setSince($since);
        $this->orderCheck();
        if ($since !== null)
        {
            $newDataSet = array();
            for ($i = 0; $i < count($this->data); $i++)
            {
                if ($this->data[$i]->time < $this->since) {
                    $this->since = null; //reset since
                    return $newDataSet;
                }
                $newDataSet[$i] = $this->data[$i];
            }
        }
        return $this->data;
    }
    protected function result($data){
        return $data;
    }

    protected function decode($encodedColumn){
        foreach($this->data as $row => $columns){
            if (!key_exists($encodedColumn, $columns))return false;
            if (is_object($columns)){
                if(!is_string($columns->$encodedColumn))return false;
                $this->data[$row]->$encodedColumn = json_decode($columns->$encodedColumn);
            } else {
                if(!is_string($columns[$encodedColumn]))return false;
                $this->data[$row][$encodedColumn] = json_decode($columns[$encodedColumn]);
            }
        }
        return true;
    }

    protected function setSince($since)
    {
        $this->since =  (is_object($since)) ? null : (int)$since;
    }
    protected function orderCheck()
    {
        bdump(($this->data[0] instanceof Action));
        if (is_object($this->data))return false;
        $sort = array();
        foreach ( $this->data as $row => $columns){
            if (!key_exists('time', $columns)) return false;
            $sort[$row] = $columns['time'];
        }
        array_multisort($sort, SORT_DESC, $this->data);
        return true;
    }
}