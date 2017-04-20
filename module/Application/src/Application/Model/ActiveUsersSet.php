<?php
namespace Application\Model;


use Application\Model\BasicModels\StatDataSetBasic;

class ActiveUsersSet
    extends StatDataSetBasic
{    
    private $guestsAllOver = 0;
    private $expireDuration = 30*60; // UNIX timestamp based

    /**** SET ****/
    public function updateItem($data)
    {
        $data['expireDuration'] = $this->expireDuration;
        if ($this->newBuild) {
            $this->create( $data );
            bdump('init catch');
            return true;
        }
        if ( key_exists($data['sid'], $this->data) ) $this->update($data);
        else $this->create($data);
        $this->setHashOfNewItem($data['sid']);
        $this->deleteExpired($data['time']);
    }

    /**** GET ****/
    public function getActiveGuests(){
        if($this->data == null) return null;
        $result = 0;
        foreach ($this->data as $item)
        {
            if ($item->userId == 0) $result++;
        }
        return $result;
    }
    public function getGuestCount(){
        return $this->guestsAllOver;
    }

    /**** PRIVATE METHODS ****/
    private function update ($data)
    {
        $itemId = $sid = $data['sid'];
        $this->removeHashEntries($itemId);
        // update
        if($this->data[$sid]->userId == 0 && $data['userId'] !==0)$this->guestsAllOver--;
        $this->data[$sid]->update($data['ip'], $data['userId'], $data['userName'], $data['url'], $data['time'], $data['data']);
    }

    private function create($createData)
    {
        $url = $userId = $time = $sid = $ip = $userName = $expireDuration = false;
        $data = null;
        foreach ($createData as $key => $value)
            $$key = $value;
        if ($userId == 0)$this->guestsAllOver++;
        $this->data[$sid] = new ActiveUser($sid, $url, $userId, $time, $sid, $ip, $userName, $expireDuration, $data);
    }
    private function deleteExpired($time)
    {
        foreach ($this->data as $sid => $item)
            if ($time > $item->expires)
            {
                unset($this->data[$sid]);
                $this->removeHashEntries($sid);
            }
    }
}