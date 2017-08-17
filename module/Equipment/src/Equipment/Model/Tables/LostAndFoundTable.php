<?php
namespace Equipment\Model\Tables;

use Application\Model\AbstractModels\DatabaseTable;
use Equipment\Hydrator\EquipmentResultSet;
use Equipment\Hydrator\LostAndFoundResultSet;
use Equipment\Model\DataModels\Equip;
use Equipment\Model\DataModels\LostAndFoundItem;
use Zend\Db\Adapter\Adapter;

class LostAndFoundTable extends DatabaseTable
{
    public $table = 'lost_and_found';

    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, LostAndFoundItem::class);
        //create hydrator
        // set naming strategy            https://framework.zend.com/manual/2.4/en/modules/zend.stdlib.hydrator.namingstrategy.underscorenamingstrategy.html
//        $this->hydrator->setNamingStrategy(new UnderscoreNamingStrategy());
        // set strategies                 https://framework.zend.com/manual/2.4/en/modules/zend.stdlib.hydrator.strategy.html
//        $this->hydrator->addStrategy("data", new SerializableStrategy());
        // set filter                     https://framework.zend.com/manual/2.4/en/modules/zend.stdlib.hydrator.strategy.html
        //@todo add example

        $this->resultSetPrototype = new LostAndFoundResultSet();
        $this->initialize();
    }

    //========================== Overrides

    protected function prepareSelect($select)
    {
        $select->columns(array(
                'id' 		=> 'id',
                'name' 		=> 'name',
                'possessed' => 'possessed',
				'might_be' 	=> 'might_be',
                'image' 	=> 'image',
                'event' 	=> 'event',
				'lost'		=> 'lost',
				'claimed'	=> 'claimed'

            ))
            ->join(array(
                'users' => 'users'                        // second table (alias => table possible)
            ),
                'possessed = users.id',    // join where
                array(                          // other columns (alias => column possible)
                    'user_name' => 'name',
                ), 'left');
        return $select;
    }

    /**
     * extracts the db column values from given object
     *
     * @param LostAndFoundItem $data
	 *
     * @return array for db save|add
     */
    protected function prepareDataForSave($data){
        return array(
            'id'    	=> (int) $data->id,
            'name'  	=> $data->name,
			'possessed' => (int) $data->possessed,
			'might_be' 	=> $data->mightBe,
			'image' 	=> $data->image,
			'event' 	=> $data->event,
			'lost'		=> (int) $data->lost,
        );
    }
}
