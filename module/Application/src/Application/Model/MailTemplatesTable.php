<?php
namespace Application\Model;

use Exception;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class MailTemplatesTable extends AbstractTableGateway
{

    public $table = 'mail_templates';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet(ResultSet::TYPE_ARRAY);
        $this->initialize();
    }

    public function getAllTemplates()
    {
        return $this->select()->toArray();
//        try {
//            $sql = new Sql($this->getAdapter());
//            $select = $sql->select();
//            $statement = $sql->prepareStatementForSqlObject($select);
//            $templates = $this->resultSetPrototype->initialize($statement->execute())
//                ->toArray();
//            return $templates;
//        } catch (\Exception $e) {
//            throw new \Exception($e->getPrevious()->getMessage());
//        }
    }

    public function getByID($id)
    {
        $result = $this->select([
            'id' => $id
        ])->toArray();
        if (count($result) < 1) {
            return null;
        }
        return $result[0];
    }

    public function save(Array $data)
    {
        $id = $data['id'];
        if (!isset($data['id']) || $id == 0) {
            $this->insert($data);
        } else {
            if ($this->getByID($id)) {
                if( $this->isBuildIn($id) ) return false;
                $this->update($data, array('id' => $id));
            } else {
                throw new Exception('User id does not exist');
            }
        }
        return $this->lastInsertValue;
    }

    public function deleteByID($id)
    {
        if( $this->isBuildIn($id) ) return false;
        return $this->delete([
                'id' => $id
            ]);
    }
    public function isBuildIn($id) {
        $entry = $this->getByID($id);
        if ($entry === null) return false;
        if ($entry['build_in'] == 1) return true;
        return false;
    }
}
