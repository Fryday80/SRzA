<?php
namespace Application\Model;


class ActiveUsersSet
    extends BasicStatDataSet
{    
    private $activeUsersSet = array();
    private $hashLeaseSid = array();
    private $hashTimeSid = array();
    private $guestsAllOver = 0;
    private $expireTime = 30*60; // UNIX timestamp based

    public function updateActive($sid, $ip, $lastActionUrl, $data = null)
    {
        $time = time();
        if ( key_exists($sid, $this->activeUsersSet) ) $this->update($sid, $ip, $lastActionUrl, $time, $data);
        else $this->create($sid, $ip, $lastActionUrl, $time, $data);
        $this->hashLeaseSid[$this->activeUsersSet[$sid]->expires] = $sid;
        $this->hashTimeSid[$time] = $sid;
        $this->deleteExpired($time);
    }
    
    public function getActiveGuests(){
        if($this->activeUsersSet !== null) return null;
        $result = 0;
        foreach ($this->activeUsersSet as $item)
        {
            if ($item->userId == 0) $result++;
        }
        return $result;
    }
    
    public function getGuestCount(){
        return $this->guestsAllOver;
    }

    public function getActiveUsers()
    {
        if (!isset($this->hashTimeSid)) return null;
        krsort($this->hashTimeSid);
        $result = array();
        foreach ($this->hashTimeSid as $sid) array_push($result, $this->activeUsersSet[$sid]);
        return $result;
    }

    /**** PRIVATE METHODS ****/
    private function update ($sid, $ip, $lastActionUrl, $time, $data = null)
    {
        if($this->activeUsersSet[$sid]->userId == 0 && $this->userId() !==0)$this->guestsAllOver--;
        $this->activeUsersSet[$sid]->update($ip, $this->userId(), $this->userName(), $lastActionUrl, $time, $data);
    }

    private function create($sid, $ip, $lastActionUrl, $time, $data = null)
    {
        if ($this->userId() == 0)$this->guestsAllOver++;
        $this->activeUsersSet[$sid] = new ActiveUser($this->expireTime, $sid, $ip, $this->userId(), $this->userName(), $lastActionUrl, $time, $data);
    }

    private function deleteExpired($time)
    {
        foreach ($this->hashLeaseSid as $expire => $sid)
        {
            if ($expire < $time)
            {
                unset($this->hashTimeSid[$this->activeUsersSet[$sid]->time]);
                unset($this->activeUsersSet[$sid]);
                unset($this->hashLeaseSid[$expire]);
            }
        }
    }
}