<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 20.04.2017
 * Time: 12:46
 */

namespace Application\Model\BasicModels;


class StatsCollectionBasic
{
    public $realData; 
    /** @var  $activeSet StatDataSetBasic */
    protected $activeSet;
    protected $data;
    protected $hash;
    
    public function getData($setName, $since = 0){
        $this->fetchDataSet($setName);
        if ($since == 0) return $this->data;
        return $this->getSince($since);
    }
    public function getDataById ($setName, $id){
        $this->fetchDataSet($setName);
        return $this->getItemById($id);
    }
    public function getDataByKey($setName, $key, $value){
        $this->fetchDataSet($setName);
        return $this->getByKey($key, $value);
    }

    protected function fetchDataSet($setName = "std"){
        $this->activeSet = $this->realData[$setName]['SET'];
        $this->data = $this->activeSet->toArray();
        $this->hash = $this->realData[$setName]['hash'];
    }
    public function toArray(){
        return $this->data;
    }
    protected function getItemById($id){
        return $this->activeSet->getItemById($id);
    }
    protected function getByKey($key, $value){
        $result = array();
        foreach ($this->data as $item)
            if ($item->$key == $value) array_push($result, $item);
        return $result;
    }
    protected function getSince($since){
        $list = array();
        $result = array();
        foreach ($this->data as $item)
            if ($item->time > $since) {
                if (!isset ($list[$item->time])) $list[$item->time] = array();
                array_push($list[$item->time], $item);
            }
        krsort($list);
        foreach ( $list as $timeKey => $itemArray)
            foreach ($itemArray as $item)
                array_push($result, $item);
        return $result;
    }
}