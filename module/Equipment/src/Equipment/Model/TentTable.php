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
        $row = $this->select();
        if (!$row)
            return false;

        return $row->toArray();
    }

    /**
     * @param $id
     * @return bool| array
     */
    public function getById($id) {
        $row = $this->select(array('id' => (int) $id));
        if (!$row)
            return false;
        
        return $row->toArray()[0];
    }

    /**
     * @param $id
     * @return bool| array
     */
    public function getByUserId($id) {
        $row = $this->select(array('user_id' => (int) $id));
        if (!$row)
            return false;

        return $row->toArray();
    }

    public function add(Tent $tentData) {
        if (!$this->insert(array(
                'color' => $tentData->color,
                'userId' => $tentData->userId,
                'shape' => $tentData->shape,
                'width' => $tentData->width,
                'length' => $tentData->length,
                'spareBeds' => $tentData->spareBeds,
                'isShowTent' => $tentData->isShowTent,
                'isGroupEquip' => $tentData->isGroupEquip,
        )))
            return false;
        return $this->getLastInsertValue();
    }

    public function save(Tent $tentData) {
        if ($tentData->id == null) $this->add($tentData);
        if ( !$this->update(
            //data
            array(
                'color' => $tentData->color,
                'userId' => $tentData->userId,
                'shape' => $tentData->shape,
                'width' => $tentData->width,
                'length' => $tentData->length,
                'spareBeds' => $tentData->spareBeds,
                'isShowTent' => $tentData->isShowTent,
                'isGroupEquip' => $tentData->isGroupEquip,
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
