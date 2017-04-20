<?php

namespace Application\Model\BasicModels;

class StatDataSetBasic
{
    public $newBuild = true;
    protected $nextId =1;
    protected $data = array();
    protected $hash = array( 'nextId' => 1, 'list' => array(), 'hashTables' => array('bsp'=>'bsp') );

    public function toArray(){
        return $this->data;
    }
    public function getItemById($id){
        foreach ($this->data as $item)
            if ($item->itemId == $id) return $item;
        return false;
    }
    public function getHashTables(){
        if($this->newBuild)return null;
        else return array('list' => $this->hash['list'], 'hashtables' => $this->hash['hashtables']);
    }
    
    protected function getSince($since)
    {
        if (! isset( $this->data[0] ) ) return null;
        $since = (is_object($since)) ? 0 : (int)$since;
        $newDataSet = array();
        $i = 0;
        while ( ( $i < count($this->data) ) && ( $this->data[$i]->time >= $since ) )
        {
            $newDataSet[$i] = $this->data[$i];
            $i++;
        }
        return $newDataSet;
    }
    protected function setHashOfNewItem($itemId)
    {
        $item = $this->getItemById($itemId);
        $hid = 0;
        foreach ($item as $keyName => $value) {
            if ($keyName == 'id') continue;
            $hid = array_search($keyName, $this->hash['list']);
            if ($hid == false) {
                // set new hash information
                $hid = $this->hash['nextId'];
                $this->hash['nextId']++;
                $this->hash['list'][$hid] = $keyName;
                $this->hash['hashtables'][$hid] = array();
            }
            if (!isset($this->hash['hashtables'][$hid][$value])) $this->hash['hashtables'][$hid][$value] = array();
            array_push($this->hash['hashtables'][$hid][$value], $itemId);
        }
    }
    protected function getHashTableByKey($key){
        if($this->newBuild)return false;
        else {
            $hid = array_search($key, $this->hash['list']);
            return $this->hash['hashtables'][$hid];
        }
    }
    protected function removeHashEntries($id){
        foreach ($this->hash['hashtables'] as $tkey => $hashtable)
            foreach ($hashtable as $key => $itemId)
                if ($itemId == $id) unset ($this->hash['hashtables'][$tkey][$key]);
    }
    protected function nextId(){
        $next = $this->nextId;
        $this->nextId++;
        return (int)$next;
    }
}