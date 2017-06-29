<?php
namespace Application\Model;

use Application\Hydrator\HydratingResultSet;
use Application\Hydrator\Hydrator;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
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
        $this->hydrator->setNamingStrategy(new UnderscoreNamingStrategy());
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
    public function save($id, $data) {

        var_dump($id);
        var_dump($data);
        if ( !$this->update($data, array( 'id' => (int)$id)) )
            return false;
        return $id;
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
    public function update($set, $where = null, array $joins = null)
    {
        if (!$this->isInitialized) {
            $this->initialize();
        }
        $sql = $this->sql;
        $update = $sql->update();
        $update->set($set);
        if ($where !== null) {
            $update->where($where);
        }

        if ($joins) {
            foreach ($joins as $join) {
                $type = isset($join['type']) ? $join['type'] : Join::JOIN_INNER;
                $update->join($join['name'], $join['on'], $type);
            }
        }

        return $this->executeUpdate($update);
    }
}
