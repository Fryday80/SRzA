<?php
namespace Equipment\Model;

use Application\Model\DataObjects\DataItem;
use Application\Model\DataSet;
use Zend\Db\ResultSet\AbstractResultSet;
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
        return $this->getSome();
    }
    public function getAllByType($type){
        return $this->getSome(array('type' => $type));
    }
    
    public function getById($id) {
        return $this->getOne(array('id' => (int) $id));
    }

    public function getByUserId($id)
    {
        return $this->getSome(array('user_id' => (int) $id));
    }

    public function getByUserIdAndType($id, $type)
    {
        return $this->getSome(array('user_id' => (int) $id, 'type' => (int)$type));
    }

    public function add(EquipmentStdDataItemModel $data) {
        if (!$this->insert(array(
            'data'  => serialize ($data),
            'type'  => (int)$data->itemType,
            'image' => $data->image,
            'user_id' => $data->userId
        )))
            return false;
        return $this->getLastInsertValue();
    }

    public function save(EquipmentStdDataItemModel $data) {
        if ( !$this->update(
            array(
                'data'  => serialize($data),
                'type'  => (int)$data->itemType,
                'image' => $data->image,
                'user_id' => $data->userId
            ),
            //where
            array( 'id' => (int)$data->id )
        ) )
            return false;
        return $data->id;
    }

    public function removeById($id) {
        return ($this->delete(array('id' => (int)$id)))? $id : false;
    }

    public function removeByUserIdAndType($userId, $type)
    {
        return ($this->delete(array('user_id' => (int)$userId, 'type' => $type)))? true : false;
    }

    public function removeByUserId($userId)
    {
        return ($this->delete(array('user_id' => (int)$userId)))? true : false;
    }



    /**
     * returns all characters and there equipment
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
                ->where($where);                   // where from data set...

            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $this->resultSetPrototype->initialize($statement->execute())
                ->toArray();
            return $result;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }


    
    /**
     * @param AbstractResultSet $result
     * @return bool|DataItem[]
     */
    private function refactorResults(AbstractResultSet $result)
    {
        $result = $result->toArray();
        if (empty($result))
            return false;

        $return = array();
        foreach ($result as $item) {
            $refItem = unserialize($item['data']);
            $refItem->id = $item['id'];
            $return[] = $refItem;
        }
        return $return;
    }

    private function getOne($by)
    {
        $result = $this->select($by);
        if (!$result)
            return false;
        $result = $this->refactorResults($result);
        return $result[0];
    }

    private function getSome($by = null)
    {
        $result = $this->select($by);
        if (!$result)
            return false;
        $result = $this->refactorResults($result);
        return new DataSet($result);
    }
}
