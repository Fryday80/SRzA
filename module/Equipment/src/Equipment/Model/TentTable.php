<?php
namespace Equipment\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;

class TentTable extends AbstractTableGateway
{

    public $table = 'tent';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function getAll () {
        $result = $this->select();
        if (!$result)
            return false;
        
        return new TentSet($result->toArray());
    }

    /**
     * @param $id
     * @return bool| array
     */
    public function getById($id) {
        $row = $this->select(array('id' => (int) $id));
        if (!$row)
            return false;
        
        return new Tent($row->toArray()[0]);
    }

    /**
     * @param $id
     * @return bool| array
     */
    public function getByUserId($id) {
        $result = $this->select(array('user_id' => (int) $id));
        if (!$result)
            return false;

        return new TentSet($result->toArray());
    }

    public function add(Tent $tentData) {
        if (!$this->insert(array(
            'user_id' => $tentData->userId,
            'shape' => $tentData->shape,
            'type' => $tentData->type,
            'color1' => $tentData->color1,
            'color2' => $tentData->color2,
            'width' => $tentData->width,
            'length' => $tentData->length,
            'spare_beds' => $tentData->spareBeds,
            'is_show_tent' => $tentData->isShowTent,
            'is_group_equip' => $tentData->isGroupEquip,
        )))
            return false;
        return $this->getLastInsertValue();
    }

    public function save(Tent $tentData) {
        if ($tentData->id == null) $this->add($tentData);
        if ( !$this->update(
            //data
            array(
                'user_id' => $tentData->userId,
                'shape' => $tentData->shape,
                'type' => $tentData->type,
                'color1' => $tentData->color1,
                'color2' => ($tentData->biColor) ? $tentData->color2 : null,
                'width' => $tentData->width,
                'length' => $tentData->length,
                'spare_beds' => $tentData->spareBeds,
                'is_show_tent' => $tentData->isShowTent,
                'is_group_equip' => $tentData->isGroupEquip,
            ),
            //where
            array( 'id' => $tentData->id )
        ) )
            return false;
        return $tentData->id;
    }

    public function removeByUserId($userId)
    {
        return ($this->delete(array('user_id' => (int)$userId)))? true : false;
    }

    public function removeById($id) {
        return ($this->delete(array('id' => (int)$id)))? $id : false;
    }

}
