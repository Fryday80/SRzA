<?php
namespace Application\Model;


use Application\Model\DataObjects\DataItem;
use Zend\Paginator\Adapter\Iterator;

class DataSet
//    extends Iterator
{
    public $data = array();
    protected $class;

    public function __construct($data = null, $subClass = null)
    {
        $this->class = $subClass;
        if ($this->class == null) $this->class = DataItem::class;

        if ($data !== null){
            if (is_object($data))
                $this->addItem($data);
            elseif (is_array($data))
                $this->setData($data);       
        }
    }

    public function addItem($item)
    {
        if (is_object($item))
            $this->data[] = $item;
        else
            $this->data[] = new $this->class ($item);
    }

    public function add(Array $data)
    {
        foreach ($data as $item)
            $this->addItem($item);
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