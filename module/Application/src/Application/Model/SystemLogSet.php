<?php
namespace Application\Model;



class SystemLogSet
    extends BasicStatDataSet
{    
    private $systemLogSet;
    private $hashTimeId;
    private $hashTypeId;
    private $hashUserIdId;
    
    public function updateSystemLog($type, $msg, $data){
        $nextId = count($this->systemLogSet);
        $now = time();
        $user = $this->getUserId();
        $this->systemLogSet[$nextId] = new SystemLog( (int)$nextId, $type, (int)$now, $msg, (int)$user, $data );
        if(!isset($this->hashTypeId[$type]))$this->hashTypeId[$type] = array();
        array_push($this->hashTypeId[$type], $nextId);
        if(!isset($this->hashTimeId[$now]))$this->hashTimeId[$now] = array();
        array_push($this->hashTimeId[$now], $nextId);
        if(!isset($this->hashUserIdId[$user]))$this->hashUserIdId[$user] = array();
        array_push($this->hashUserIdId[$user], $nextId);
    }

    public function getSystemLog ($since = null){
        if ($since == null) return $this->fetchLogData();
        return $this->getSince($since);
    }

    public function getSystemLogByType ($type, $since = null)
    {
        if ($since !== null) $newData = $this->fetchLogData();
        else $newData = $this->getSince($since);
        return $this->getByKey($type, $newData, $this->hashTypeId);
    }

    public function getSystemLogByUser ($userId, $since = null)
    {
        if ($since !== null) $newData = $this->fetchLogData();
        else $newData = $this->getSince($since);
        return $this->getByKey($userId, $newData, $this->hashUserIdId);
    }

    /**** PRIVATE METHODS ****/
    private function fetchLogData(){
        return array_reverse($this->systemLogSet);
    }

    private function getByKey($key, $data, $hashTable)
    {
        $result = array();
        $ids = $hashTable[$key];
        if ( count($ids) < 1 ) return null;
        foreach ($data as $id => $item){
            if (in_array($id, $ids)) array_push($result, $item);
        }
        return $result;
    }

    private function getSince($since)
    {
        krsort($this->hashTimeId);
        $result = array();
        foreach($this->hashTimeId as $time => $idArray){
            if ($time > $since) {
                foreach ($idArray as $key => $id) array_push($result, $this->systemLogSet[$id]);
            }
            else break;
        }
        return $result;
    }
}