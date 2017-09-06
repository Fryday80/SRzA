<?php
namespace Equipment\Model;

use Application\Model\AbstractModels\DatabaseTable;
use Equipment\Hydrator\EquipmentResultSet;
use Equipment\Model\DataModels\Equip;
use Zend\Db\Adapter\Adapter;

class EquipTable extends DatabaseTable
{
    public $table = 'equip';

    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, Equip::class);
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
        return $this->select(array('item_type' => $type));
    }

	public function getAllPlannerObjects()
	{
		$result = $this->select(array('site_planner_object' => 1));
		if (!$result)
			return false;

		return $result->toObjectArray();
	}

    public function getByUserId($id)
    {
        return $this->select(array('equip.user_id' => (int) $id));
    }

    public function getByUserIdAndType($id, $type)
    {
        return $this->select(array('equip.user_id' => (int) $id, 'item_type' => (int)$type));
    }

    public function removeByUserIdAndType($userId, $type)
    {
        return ($this->delete(array('user_id' => (int)$userId, 'item_type' => $type)))? true : false;
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
                'item_type' => 'item_type',
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
     * @param Equip $data
     * @return array for db save|add
     */
    protected function prepareDataForSave($data){
        return array(
            'id'    => (int) $data['id'],
            'data'  => serialize ($data),
            'item_type'  => (int)$data['itemType'],
            'image' => $data['image'],
            'user_id' => $data['userId'],
            'site_planner_object' => $data['sitePlannerObject']
        );
    }
}