<?php

namespace Equipment\Model;

use Equipment\Model\Tent;

class TentSet
{
    private $data = array();

    public function __construct($arrayOfTents)
    {
        $this->addTents($arrayOfTents);
    }

    public function add(Tent $tent)
    {
        $this->data[] = $tent;
    }

    public function addTents(Array $arrayOfTents)
    {
        foreach ($arrayOfTents as $tent) {
            if ($tent instanceof Tent) $this->data[] = $tent;
            else $this->data[] = new Tent ($tent);
        }
    }

    public function setData(Array $arrayOfTents)
    {
        $this->data = array();
        $this->addTents($arrayOfTents);
    }

    public function toArrayOfTents()
    {
        return $this->data;
    }

    public function toArray()
    {
        $return = array();
        foreach ($this->data as $tent) {
            $return[] = get_object_vars($tent);
        }
        return $return;
    }
}