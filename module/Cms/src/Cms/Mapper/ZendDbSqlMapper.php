<?php
namespace Cms\Mapper;

use Cms\Model\PostInterface;
use Zend\Db\Adapter\AdapterInterface;
use Cms\Mapper\PostMapperInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Adapter\Driver\Pdo\Result;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Db\Sql\Update;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Delete;

class ZendDbSqlMapper implements PostMapperInterface
{

    /**
     *
     * @var \Zend\Db\Adapter\AdapterInterface
     */
    protected $dbAdapter;

    /**
     *
     * @var \Zend\Stdlib\Hydrator\HydratorInterface
     */
    protected $hydrator;

    /**
     *
     * @var \Cms\Model\PostInterface
     */
    protected $postPrototype;

    /**
     *
     * @param AdapterInterface $dbAdapter            
     * @param HydratorInterface $hydrator            
     * @param PostInterface $postPrototype            
     */
    public function __construct(AdapterInterface $dbAdapter, HydratorInterface $hydrator, PostInterface $postPrototype)
    {
        $this->dbAdapter = $dbAdapter;
        $this->hydrator = $hydrator;
        $this->postPrototype = $postPrototype;
    }

    /**
     *
     * @param int|string $id            
     *
     * @return PostInterface
     * @throws \InvalidArgumentException
     */
    public function find($id)
    {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select('pages');
        $select->where(array(
            'id = ?' => $id
        ));
        
        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        
        if ($result instanceof Result && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), $this->postPrototype);
        }
        
        throw new \InvalidArgumentException("Page with given ID:{$id} not found.");
    }

    /**
     *
     * @param string $url
     *
     * @return PostInterface
     * @throws \InvalidArgumentException
     */
    public function findByUrl($url)
    {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select('pages');
        $select->where(array(
            'url = ?' => $url
        ));

        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();

        if ($result instanceof Result && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), $this->postPrototype);
        }

        throw new \InvalidArgumentException("Page with given Url:{$url} not found.");
    }

    /**
     *
     * @return array|PostInterface[]
     */
    public function findAll()
    {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select('pages');
        
        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        
        if ($result instanceof Result && $result->isQueryResult()) {
            $resultSet = new HydratingResultSet($this->hydrator, $this->postPrototype);
            
            return $resultSet->initialize($result);
        }
        
        return array();
    }

    /**
     *
     * @param PostInterface $postObject            
     *
     * @return PostInterface
     * @throws \Exception
     */
    public function save(PostInterface $postObject)
    {
        $postData = $this->hydrator->extract($postObject);
        unset($postData['id']); // Neither Insert nor Update needs the ID in the array
        
        if ($postObject->getId()) {
            // ID present, it's an Update
            $action = new Update('pages');
            $action->set($postData);
            $action->where(array(
                'id = ?' => $postObject->getId()
            ));
        } else {
            // ID NOT present, it's an Insert
            $action = new Insert('pages');
            $action->values($postData);
        }
        
        $sql = new Sql($this->dbAdapter);
        $stmt = $sql->prepareStatementForSqlObject($action);
        $result = $stmt->execute();
        
        if ($result instanceof Result) {
            if ($newId = $result->getGeneratedValue()) {
                // When a value has been generated, set it on the object
                $postObject->setId($newId);
            }
            
            return $postObject;
        }
        
        throw new \Exception("Database error");
    }

    /**
     *
     * {@inheritdoc}
     *
     */
    public function delete(PostInterface $postObject)
    {
        $action = new Delete('pages');
        $action->where(array(
            'id = ?' => $postObject->getId()
        ));
        
        $sql = new Sql($this->dbAdapter);
        $stmt = $sql->prepareStatementForSqlObject($action);
        $result = $stmt->execute();
        
        return (bool) $result->getAffectedRows();
    }
}