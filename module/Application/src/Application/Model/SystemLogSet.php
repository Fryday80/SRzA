<?php
namespace Application\Model;



use Application\Model\BasicModels\StatDataSetBasic;

class SystemLogSet
    extends StatDataSetBasic
{    

    /**** SET ****/
    public function updateSystemLog($data){
        $id = $this->nextId;
        $this->systemLogSet[$id] = $this->create($data);
        $this->setHashOfNewItem($id);
    }

    /**** GET ****/
    public function toArray ($since = null){
        if ($since == null) return $this->fetchLogData();
        return $this->getSince($since);
    }

    //@todo
    public function getSystemLogByType ($type, $since = null)
    {
        if ($since !== null) $newData = $this->fetchLogData();
        else $newData = $this->getSince($since);
        return $this->getByKey($type, $newData, $this->hashTypeId);
    }

    //@todo
    public function getSystemLogByUser ($userId, $since = null)
    {
        if ($since !== null) $newData = $this->fetchLogData();
        else $newData = $this->getSince($since);
        return $this->getByKey($userId, $newData, $this->hashUserIdId);
    }
    public function getNumberOfLogs(){
        return count($this->data);
    }

    /**** PRIVATE METHODS ****/
    //@todo
    private function fetchLogData(){
        return array_reverse($this->systemLogSet);
    }

    //@todo
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

    protected function getSince($since)
    {
        $newData = $this->fetchLogData();
        $result = array();
        foreach($newData as $item){
            if ($item->time > $since) {
                array_push($result, $item);
            }
            else break;
        }
        return $result;
    }
    private function create($createData){
        $url = $time = $type = $msg = $userId = $userName = false;
        $data = null;
        foreach ($createData as $key => $value)
            $$key = $value;
        if ($url && $time && $type && $msg && $userId && $userName)return null;
        return new SystemLog( $this->nextId(), $url, $time, $type, $msg, $userId, $userName, $data );
    }
}