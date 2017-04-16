<?php
namespace Auth\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\ServiceManager\ServiceManager;

class UserTable extends AbstractTableGateway
{

    public $table = 'users';
    /**
     * @var ServiceManager ServiceManager
     */
    public $serviceManager;
    
    public function __construct(Adapter $adapter, ResultSetInterface $resultSetPrototype, ServiceManager $serviceManager)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = $resultSetPrototype;
        $this->serviceManager = $serviceManager;
        $this->initialize();
    }
    public function getUsersForAuth($email) {
        try {
            $sql = $this->getSql();
            $select = $sql->select();
            $select->where("email = '$email'");
            $select->join(array('role' => 'role'), 'users.role_id = role.rid', array('role_name'), 'LEFT');
            $user = $this->selectWith($select)->current();
            if (!$user) {
                throw new \Exception("Could not find user with email: $email");
            }
            return $user;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
            die;
        }
    }

    public function getUsers() {
        return $this->getUsersWhere();
    }
    public function getUsersBy($columnName, $value) {
        $rowset = $this->getUsersWhere(array($columnName => $value));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find user with $columnName: $value");
        }
        return $row;
    }
    public function getUser($id)
    {
        $id  = (int) $id;
        $rowset = $this->getUsersWhere(array('users.id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }
    public function getUserByMail($mail)
    {
        $rowset = $this->getUsersWhere(array('users.email' => $mail));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }
    public function saveUser(User $user)
    {
        $data = get_object_vars($user);
        unset($data['role_name']);
        $id = (int) $user->id;
        if ($id == 0) {
            $this->insert($data);
        } else {
            if ($this->getUser($id)) {
                $this->update($data, array('id' => $id));
            } else {
                throw new \Exception('User id does not exist');
            }
        }
    }
    public function deleteUser($id)
    {
        $this->delete(array('id' => (int) $id));
        //@todo remove characters from cast
    }

    public function getUsersWhere($where = array(), $columns = array())
    {
        try {
            $sql = $this->getSql();
            $select = $sql->select();

            if (count($where) > 0) {
                $select->where($where);
            }

            if (count($columns) > 0) {
                $select->columns($columns);
            }
            $select->join(array('role' => 'role'), 'users.role_id = role.rid', array('role_name'), 'LEFT');

            $users = $this->selectWith($select);
            return $users;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
