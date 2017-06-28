<?php

namespace Application\Model\DataObjects;

/**
 * Class DataItem
 *
 * basic class for use with DataSet::class
 *
 * @package Application\Model\DataObjects
 */
class DataItem
{
    public $name;
    public $id;
    
    protected $dbColumns = array();

    function __construct($data = null)
    {
        if ($data !== null)
            $this->setData($data);
    }

    public function setData($data)
    {
        foreach ($data as $key => $item) {
            $setKey = $this->camelize($key);
            $this->$setKey = $item;
        }
    }

    public function toArray()
    {
        return get_object_vars($this);
    }

    public function getDBData()
    {
        $return = $this->prepareDataForDB();
        foreach ($return as $key => $value) {
            if (!empty($this->dbColumns) && !in_array($key, $this->dbColumns))
                unset($return[$key]);
        }
        return $return;
    }

    protected function camelize($string)
    {
        return str_replace("_", '', lcfirst(ucwords($string, "_")));
    }
    
    protected function subString($string)
    {
        $newString = preg_replace('/\B([A-Z])/', '_$1', $string);
        $newString = strtolower($newString);
        return $newString;
    }

    protected function prepareDataForDB($array = null)
    {
        $array = ($array == null) ? $this->toArray() : $array;
        $return = array();
        foreach ($array as $key => $value){
            $setKey = $this->subString($key);
            $value = (is_array($value)) ? $this->prepareDataForDB($value) : $value;
            $return[$setKey] = $value;
        }
        return $return;
    }
}