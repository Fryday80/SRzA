<?php
namespace Application\Model;

use Application\Hydrator\HydratingResultSet;
use Application\Hydrator\Hydrator;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Hydrator\Exception\InvalidArgumentException;
use Zend\Hydrator\NamingStrategy\UnderscoreNamingStrategy;

class DatabaseTable extends AbstractTableGateway
{
    public $hydrator;
    public $objectPrototype;

    public function __construct(Adapter $adapter, $objectPrototype, Hydrator $hydrator = null )
    {
        $this->adapter = $adapter;
        $this->objectPrototype = $objectPrototype;
        if ($hydrator === null) {
            //setDefault
            $this->hydrator = new Hydrator();
        } else {
            // use injected hydrator
            $this->hydrator = $hydrator;
        }

        // set naming strategy            https://framework.zend.com/manual/2.4/en/modules/zend.stdlib.hydrator.namingstrategy.underscorenamingstrategy.html
//        $this->hydrator->setNamingStrategy(new UnderscoreNamingStrategy());
        // set strategies                 https://framework.zend.com/manual/2.4/en/modules/zend.stdlib.hydrator.strategy.html
        ////$hydrator->addStrategy("data", new SerializableStrategy());
        // set filter                     https://framework.zend.com/manual/2.4/en/modules/zend.stdlib.hydrator.strategy.html
        //@todo add example

        $this->resultSetPrototype = new HydratingResultSet();
        $this->resultSetPrototype->setHydrator($this->hydrator);
        $this->resultSetPrototype->setObjectPrototype(new $objectPrototype());

        $this->initialize();
    }

    public function getAll() {
        $result = $this->select();
        if (!$result)
            return false;

        return $result->toObjectArray();
    }
    public function getById($id) {
        $result = $this->select(array('id' => $id));
        if (!$result)
            return false;

        return $result->current();
    }
    public function add($data) {
        if (!$this->insert($data))
            return false;
        return $this->getLastInsertValue();
    }
    public function save($data) {
        if (!isset($data['id']) || !is_integer($data['id']) || $data['id'] == 0) {
            if (!$this->insert($data) )
                return false;
            return $this->getLastInsertValue();
        }else {
            if (!$this->update($data, array( 'id' => (int)$data['id'])) )
                return false;
        }
        return true;
    }
    public function remove($id) {
        return ($this->delete(array('id' => (int)$id)))? $id : false;
    }
    public function hydrate(Array $data) {
        return $this->hydrator->hydrate($data, new $this->objectPrototype());

    }

    /**
     * Update
     *
     * @param  array $set
     * @param  string|array|\Closure $where
     * @param  null|array $joins
     * @return int
     */
    public function update($set, $where = null, array $joins = null) {
        $rowData = $this->entityToArray($set);
        return parent::update($rowData, $where, $joins);
    }
    /**
     * Insert
     *
     * @param  array $set
     * @return int
     */
    public function insert($set) {
        $set = $this->entityToArray($set);
        if (isset($set['id']) && $set['id'] == null ) {
            unset($set['id']);
        }
        return parent::insert($set);
    }

    /**
     * Uses the hydrator to convert the entity to an array.
     *
     * Use this method to ensure that you're working with an array.
     *
     * @param object $entity
     * @return array
     */
    protected function entityToArray($entity) {
        if (is_array($entity)) {
            return $entity; // cut down on duplicate code
        } elseif (is_object($entity)) {
            if (!$this->hydrator) {
                $this->hydrator = $this->getHydrator();
            }
            return $this->hydrator->extract($entity);
        }
        throw new InvalidArgumentException('Entity passed to db mapper should be an array or object.');
    }
}
