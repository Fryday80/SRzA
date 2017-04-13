<?php
namespace Auth\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class RoleTable extends AbstractTableGateway
{

    public $table = 'role';
    private $sorted = false;
    
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet(ResultSet::TYPE_ARRAY);
        $this->initialize();
    }
    
    public function getUserRoles($where = array(), $columns = array())
    {
        try {
            $sql = new Sql($this->getAdapter());
            $select = $sql->select()->from(array(
                'role' => $this->table
            ));
            $select->columns(array(
                'rid',
                'role_name',
                'role_parent',
            ));
            $select->join(array(
                    'parent' => $this->table
                ), 
                    'parent.rid = role.role_parent', array('role_parent_name' => 'role_name'), 'left'
                );
            
            if (count($where) > 0) {
                $select->where($where);
            }
            
            if (count($columns) > 0) {
                $select->columns($columns);
            }
            $statement = $sql->prepareStatementForSqlObject($select);
            $roles = $this->resultSetPrototype->initialize($statement->execute())
                ->toArray();
            return $roles;
        } catch (\Exception $e) {
            throw new \Exception($e->getPrevious()->getMessage());
        }
    }
    public function getRoleByID($id) {
        $res = $this->select("rid = $id")->toArray();
        if (count($res) > 0) {
            return $res[0];
        }
        return null;
    }
    public function edit($data, $id) {
        $this->update($data, array('rid' => $id));
    }
    public function add($name, $parent, $status) {
        $status = 'Active';//($status)? 'Active': 'Inactive';
        $this->insert(array('role_name' => $name, 'role_parent' => $parent, 'status' => $status));
    }
    public function deleteByID($id) {
        return $this->delete([
            'rid' => $id
        ]);
    }
    public function fetchAll(){
        return $this->select()->toArray();
    }
    public function fetchAllSorted(){
        if (!$this->sorted) {
            $megalomaniac = 'Administrator';
            $rearranged = array();
            $hash = array();
            $return = array();

            $all = $this->fetchAll();
            foreach ($all as $key => $role) {
                $rearranged[$role['role_name']] = $role;
                $hash[$role['rid']] = $role['role_name'];
            }
            $return[count($rearranged)] = $rearranged[$megalomaniac];

            for ($i = count($rearranged) - 1; $i > 0; $i--) {
                $return[$i - 1] = $rearranged [$hash[$i]];
            }

            foreach ($return as $role) {
                $this->sorted[$role['rid']] = $role['role_name'];
            }
        }
        return $this->sorted;
    }
}
