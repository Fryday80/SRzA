<?php
namespace Application\Model;


use Zend\Paginator\Adapter\Iterator;

class DataSet
//    extends Iterator
{
    public $data = array();
    protected $class;

    public function __construct($subClass, $data = null)
    {
        $this->class = new $subClass();//würd ich mal testen
        if ($data !== null){
            if (is_object($data))
                $this->addItem($data);
            elseif (is_array($data))
                $this->setData($data);       
        }
    }

    public function addItem($data)
    {
        $this->data[] = new $this->class ($data);
    }

    public function add(Array $data)
    {
        foreach ($data as $item) {
            $this->addItem($item);
        }
    }

    public function setData($data)
    {
        $this->data = array();
        $this->add($data);
    }
    
    public function toArray()
    {
        $return = array();
        foreach ($this->data as $item) {
            $return[] = get_object_vars($item);
        }
        return $return;
    }

    public function toArrayOfObjects()
    {
        return $this->data;
    }
}