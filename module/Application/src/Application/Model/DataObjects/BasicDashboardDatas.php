<?php

namespace Application\Model\DataObjects;


class BasicDashboardDatas
{
    public $data = array();

    function __construct($data = null)
    {
        if ($data !== null) $this->data = $data;
    }

    public function getAllResults()
    {
        return $this->data;
    }
    public function getResultSince($timestamp = null){

        if ($timestamp !== null)
        {
            $newDataSet = array();
            for ($i = 0; $i < count($this->data); $i++)
            {
                if ($this->data[$i]->time < $timestamp)
                {
                    return $newDataSet;
                }
                $newDataSet[$i] = $this->data[$i];
            }
        }
        return $this->data;
    }
}