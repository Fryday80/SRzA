<?php
namespace Equipment\Model\Tables;

use Application\Model\AbstractModels\DatabaseTable;
use Equipment\Model\DataModels\SitePlan;
use Zend\Db\Adapter\Adapter;
use Zend\Hydrator\Strategy\SerializableStrategy;

class SitePlannerTable extends DatabaseTable
{
    public $table = 'site_plan';

    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, SitePlan::class);
        //create hydrator
        // set naming strategy            https://framework.zend.com/manual/2.4/en/modules/zend.stdlib.hydrator.namingstrategy.underscorenamingstrategy.html
//        $this->hydrator->setNamingStrategy(new UnderscoreNamingStrategy());
        // set strategies                 https://framework.zend.com/manual/2.4/en/modules/zend.stdlib.hydrator.strategy.html
//        $this->hydrator->addStrategy("data", new SerializableStrategy());
        // set filter                     https://framework.zend.com/manual/2.4/en/modules/zend.stdlib.hydrator.strategy.html
        //@todo add example

        $this->initialize();
    }
}
