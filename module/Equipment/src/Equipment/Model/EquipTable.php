<?php
namespace Equipment\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;

class EquipTable extends AbstractTableGateway
{

    public $table = 'equip';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function getAll () {
        $result = $this->select();
        if (!$result)
            return false;
        
        return $result->toArray();
    }
    public function getAllByType($type){
        return $this->getSome(array('type' => $type));
    }
    
    public function getById($id) {
        return $this->getOne(array('id' => (int) $id));
    }

    public function getByUserId($id) {
        return $this->getSome(array('user_id' => (int) $id));
    }

    private function getOne($by)
    {
        $row = $this->select($by);
        if (!$row)
            return false;
        $res = $row->toArray();
        if (empty($res)) return false;
        $res[0]['data'] = unserialize($res[0]['data']);
        return $res[0];
    }

    private function getSome($by)
    {
        $result = $this->select($by);
        if (!$result)
            return false;
        $return = $result->toArray();
        foreach ($return as &$item) {
            $item['data'] = unserialize($item['data']);
        }

        return $return;
    }

    public function add($data, $type, $image = null) {
        if (!$this->insert(array(
            'data'  => serialize ($data),
            'type'  => $type,
            'image' => $image,
        )))
            return false;
        return $this->getLastInsertValue();
    }

    public function save($id, $data, $type, $image = null) {
        if ( !$this->update(
            array(
                'data'  => serialize($data),
                'type'  => $type,
                'image' => $image,
            ),
            //where
            array( 'id' => $id )
        ) )
            return false;
        return $id;
    }

    public function removeByUserId($userId)
    {
        return ($this->delete(array('user_id' => (int)$userId)))? true : false;
    }

    public function removeByUserIdAndType($userId, $type)
    {
        return ($this->delete(array('user_id' => (int)$userId, 'type' => $type)))? true : false;
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
//                    'bi_color' => 'biColor',
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
