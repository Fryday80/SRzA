<?php
namespace Equipment\Model;

use Application\Model\DataSet;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;

class SitePlannerTable extends AbstractTableGateway
{

    public $table = 'site_plan';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function getAll () {
        $result = $this->select();
        if (!$result)
            return false;
        
        return new DataSet(\Equipment\Model\Tent::class, $result->toArray());
    }

    public function add($data) {
        if (!$this->insert($data))
            return false;
        return $this->getLastInsertValue();
    }

    public function save($id, $data) {
        if ( !$this->update($data, array( 'id' => $id )
        ) )
            return false;
        return $id;
    }

    public function removeByUserId($userId)
    {
        return ($this->delete(array('user_id' => (int)$userId)))? true : false;
    }

    public function removeById($id) {
        return ($this->delete(array('id' => (int)$id)))? $id : false;
    }

//    /**
//     * returns all characters and there jobs, families and so on
//     * @param array $where
//     * @return array results
//     * @throws \Exception
//     */
//    public function fetchAllCastData(Array $where = array()) {
//        try {
//            $sql = new Sql($this->getAdapter());
//
//            $select = $sql->select()
//                ->from(array(
//                    'tent' => 'tent'                // main table (alias => table possible)
//                ))
//                ->columns(array(                    // selected columns (alias => column possible)
//                    'id' => 'id',
//                    'user_id' => 'user_id',
//                    'shape' => 'shape',
//                    'type' => 'type',
//                    'color1' => 'color1',
////                    'bi_color' => 'biColor',
//                    'color2' => 'color2',
//                    'width' => 'width',
//                    'length' => 'length',
//                    'spare_beds' => 'spare_beds',
//                    'is_show_tent' => 'is_show_tent',
//                    'is_group_equip' => 'is_group_equip',
//                ))
//                ->join(array(
//                    'types' => 'tent_types'                        // second table (alias => table possible)
//                ),
//                    'types.id = tent.type',    // join where
//                    array(                          // other columns (alias => column possible)
//                    'type_name' => 'name',
//                ), 'left')
//                ->join(array(
//                    'jusers' => 'users'                        // second table (alias => table possible)
//                ),
//                    'jusers.id = tent.user_id',    // join where
//                    array(                          // other columns (alias => column possible)
//                        'user_name' => 'name',
//                    ), 'left')
//                ->where(array());                   // where from data set...
//
//            $statement = $sql->prepareStatementForSqlObject($select);
//            $result = $this->resultSetPrototype->initialize($statement->execute())
//                ->toArray();
//            return $result;
//        } catch (\Exception $e) {
//            throw new \Exception($e->getMessage());
//        }
//    }
}
