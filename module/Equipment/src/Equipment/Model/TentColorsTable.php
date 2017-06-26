<?php
namespace Equipment\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;

class TentColorsTable extends AbstractTableGateway
{

    public $table = 'tent_colors';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function getAll () {
        $row = $this->select();
        if (!$row)
            return false;

        return $row->toArray();
    }

    public function add($data) {
        if (!$this->insert(array(
            'name' => $data['name'],
            'color1' => $data['color1'],
            'color2' => ($data['biColor'] == "1" ) ? $data['color2'] : null,
            )))
            return false;
        return $this->getLastInsertValue();
    }
    
    public function save($data) {
        if (!$this->update(array(
            'name' => $data['name'],
            'color1' => $data['color1'],
            'color2' => ($data['biColor'] == "1" ) ? $data['color2'] : null,
        ),
            array(
                'id' => (int) $data['id']
            )))
            return false;
        return $data['id'];
    }
    
    public function remove($id) {
        return ($this->delete(array('id' => (int)$id)))? $id : false;
    }

}
