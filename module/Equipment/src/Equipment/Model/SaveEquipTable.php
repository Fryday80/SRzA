<?php
namespace Equipment\Model;

use Application\Model\DataObjects\DataItem;
use Application\Model\DataSet;
use Zend\Db\ResultSet\AbstractResultSet;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;

class SEquipTable extends AbstractTableGateway
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
        return $this->getSome(array('equip.type' => $type));
    }
    
    public function getById($id) {
        return $this->getOne(array('equip.id' => (int) $id));
    }

    public function getByUserId($id)
    {
        return $this->getSome(array('equip.user_id' => (int) $id));
    }

    public function getByUserIdAndType($id, $type)
    {
        return $this->getSome(array('equip.user_id' => (int) $id, 'equip.type' => (int)$type));
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
        if (!isset($data->id) || $data->id == null)
            return $this->add($data);
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
     * DEPRECATED +++ DEPRECATED +++ replaced by getAll
     * returns all characters and there equipment
     * @return array results
     * @internal param array $where
     */
    public function fetchAllCastData() {
        bdump('DEPRECATED METHOD USED!!!');
        return $this->getSome();
    }
    
    /**
     * @param AbstractResultSet $result
     * @return bool|DataItem[]
     */
    private function refactorResults(AbstractResultSet $result)
    {
        $result = $result->toArray();
        if (empty($result)) return false;

        $return = array();
        foreach ($result as $item) {
            $refItem = unserialize($item['data']);
            $refItem->id = $item['id'];
            $refItem->userName = $item['user_name'];
            if ($refItem->userId == 0)
                $refItem->userName = 'Verein';
            $return[] = $refItem;
        }
        return $return;
    }

    private function getOne($by)
    {
        $result = $this->getData($by);
        if (!$result)
            return false;
        $result = $this->refactorResults($result);
        return $result[0];
    }

    private function getSome($by = null)
    {
        $result = $this->getData($by);
        if (!$result)
            return false;
        $result = $this->refactorResults($result);
        return new DataSet($result);
    }

    /**
     * @param array $where
     * @return array|false
     * @throws \Exception
     * @internal param EEquipTypes $type
     */
    private function getData($where = array())
    {
        try {
            $sql = new Sql($this->getAdapter());

            $select = $sql->select()
                ->from(array(
                    'equip' => 'equip'                // main table (alias => table possible)
                ))
                ->columns(array(
                    'id' => 'id',
                    'data' => 'data',
                    'type' => 'type',
                    'image' => 'image',
                    'user_id' => 'user_id',

                ))
                ->join(array(
                    'users' => 'users'                        // second table (alias => table possible)
                ),
                    'user_id = users.id',    // join where
                    array(                          // other columns (alias => column possible)
                        'user_name' => 'name',
                    ), 'left')
                ->where ($where);

            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $this->resultSetPrototype->initialize($statement->execute());
            return $result;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
