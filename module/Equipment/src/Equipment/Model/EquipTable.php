<?php
namespace Equipment\Model;

use Application\Model\DatabaseTable;
use Application\Model\DataObjects\DataItem;
use Equipment\Hydrator\EquipmentResultSet;
use Zend\Db\Adapter\Adapter;

class EquipTable extends DatabaseTable
{
    public $table = 'equip';

    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, EquipDBObject::class);
        //create hydrator
        // set naming strategy            https://framework.zend.com/manual/2.4/en/modules/zend.stdlib.hydrator.namingstrategy.underscorenamingstrategy.html
//        $this->hydrator->setNamingStrategy(new UnderscoreNamingStrategy());
        // set strategies                 https://framework.zend.com/manual/2.4/en/modules/zend.stdlib.hydrator.strategy.html
//        $this->hydrator->addStrategy("data", new SerializableStrategy());
        // set filter                     https://framework.zend.com/manual/2.4/en/modules/zend.stdlib.hydrator.strategy.html
        //@todo add example

        $this->resultSetPrototype = new EquipmentResultSet();
        $this->initialize();
    }

    public function getAllByType($type){
        return $this->select(array('equip.type' => $type));
    }

    public function getByUserId($id)
    {
        return $this->select(array('equip.user_id' => (int) $id));
    }

    public function getByUserIdAndType($id, $type)
    {
        return $this->select(array('equip.user_id' => (int) $id, 'equip.type' => (int)$type));
    }

    public function removeById($id) {
        return $this->remove($id);
    }

    public function removeByUserIdAndType($userId, $type)
    {
        return ($this->delete(array('user_id' => (int)$userId, 'type' => $type)))? true : false;
    }

    public function removeByUserId($userId)
    {
        return ($this->delete(array('user_id' => (int)$userId)))? true : false;
    }

    //========================== Overrides

    protected function prepareSelect($select)
    {
        $select->columns(array(
                'id' => 'id',
                'data' => 'data',
                'type' => 'type',
                'image' => 'image',
                'user_id' => 'user_id',
                'site_planner_object' => 'site_planner_object',

            ))
            ->join(array(
                'users' => 'users'                        // second table (alias => table possible)
            ),
                'user_id = users.id',    // join where
                array(                          // other columns (alias => column possible)
                    'user_name' => 'name',
                ), 'left');
        return $select;
    }

    /**
     * extracts the db column values from given object Tent|Equipment
     * @param EquipmentStdDataItemModel $data
     * @return array for db save|add
     */
    protected function prepareDataForSave(EquipmentStdDataItemModel $data){
        return array(
            'data'  => serialize ($data),
            'type'  => (int)$data->itemType,
            'image' => $data->image,
            'user_id' => $data->userId,
            'site_planner_object' => $data->sitePlannerObject
        );
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
}
