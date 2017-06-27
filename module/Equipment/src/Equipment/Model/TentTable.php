<?php
namespace Equipment\Model;

use Zend\Db\Sql\Sql;
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
     * @return bool| Tent
     */
    public function getById($id) {
        $row = $this->select(array('id' => (int) $id));
        if (!$row)
            return false;
        
        return new Tent($row->toArray()[0]);
    }

    /**
     * @param $id
     * @return bool| TentSet
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
            'bi_color' => $tentData->biColor,
            'color2' => $tentData->color2,
            'width' => $tentData->width,
            'length' => $tentData->length,
            'spare_beds' => $tentData->spareBeds,
            'is_show_tent' => $tentData->isShowTent,
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
                'bi_color' => $tentData->biColor,
                'color2' => ($tentData->biColor == 1) ? $tentData->color2 : $tentData->color1,
                'width' => $tentData->width,
                'length' => $tentData->length,
                'spare_beds' => $tentData->spareBeds,
                'is_show_tent' => $tentData->isShowTent,
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

    /**
     * returns all characters and there jobs, families and so on
     * @param array $where
     * @return array results
     * @throws \Exception
     */
    public function fetchAllCastData(Array $where = array()) {
        try {
            $sql = new Sql($this->getAdapter());

            $select = $sql->select()
                ->from(array(
                    'tent' => 'tent'                // main table (alias => table possible)
                ))
                ->columns(array(                    // selected columns (alias => column possible)
                    'id' => 'id',
                    'user_id' => 'user_id',
                    'shape' => 'shape',
                    'type' => 'type',
                    'color1' => 'color1',
                    'bi_color' => 'biColor',
                    'color2' => 'color2',
                    'width' => 'width',
                    'length' => 'length',
                    'spare_beds' => 'spare_beds',
                    'is_show_tent' => 'is_show_tent',
                    'is_group_equip' => 'is_group_equip',
                ))
                ->join(array(
                    'types' => 'tent_types'                        // second table (alias => table possible)
                ),
                    'types.id = tent.type',    // join where
                    array(                          // other columns (alias => column possible)
                    'type_name' => 'name',
                ), 'left')
                ->join(array(
                    'jusers' => 'users'                        // second table (alias => table possible)
                ),
                    'jusers.id = tent.user_id',    // join where
                    array(                          // other columns (alias => column possible)
                        'user_name' => 'name',
                    ), 'left')
                ->where(array());                   // where from data set...

            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $this->resultSetPrototype->initialize($statement->execute())
                ->toArray();
            return $result;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

}
