<?php

namespace Equipment\Models\DataObjects\Groups;


use Equipment\Models\DataObjects\Single\Tent;

class TentSet
{
    private $data = array();

    public function add(Tent $tent)
    {
        $this->data[] = $tent;
    }

    public function addTents(Array $arrayOfTents)
    {
        foreach ($arrayOfTents as $tent) {
            if ($tent instanceof Tent) $this->data[] = $tent;
            else {
                bdump('item must be instance of Tent!');
                continue;
            }
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