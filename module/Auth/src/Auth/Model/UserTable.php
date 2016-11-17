<?php
namespace Auth\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\ResultSetInterface;

class UserTable extends AbstractTableGateway
{

    public $table = 'users';
    
    public function __construct(Adapter $adapter, ResultSetInterface $resultSetPrototype)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = $resultSetPrototype;
        $this->initialize();
    }
    public function getUsersForAuth($email) {
        try {
            $sql = $this->getSql();
            $select = $sql->select();
            $select->where("email = '$email'");
//             if (count($columns) > 0) {
//                 $select->columns($columns);
//             }

            $select->join(array('userRole' => 'user_role'), 'userRole.user_id = users.id', array('role_id'), 'LEFT');
            $select->join(array('role' => 'role'), 'userRole.role_id = role.rid', array('role_name'), 'LEFT');

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
    public function getUsers($where = array(), $columns = array())
    {
        try {
            $sql = $this->getSql();
            $select = $sql->select();
//                 )->from(array(
//                 'users' => $this->table
//             ));
            
            if (count($where) > 0) {
                $select->where($where);
            }
            
            if (count($columns) > 0) {
                $select->columns($columns);
            }
            
            $select->join(array('userRole' => 'user_role'), 'userRole.user_id = users.id', array('role_id'), 'LEFT');
            $select->join(array('role' => 'role'), 'userRole.role_id = role.rid', array('role_name'), 'LEFT');
            
            $users = $this->selectWith($select);
            return $users;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
    public function fetchAll()
    {
        $resultSet = $this->select();
        return $resultSet;
    }

    public function getUser($id)
    {
        $id  = (int) $id;
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find user with id: $id");
        }
        return $row;
    }
    public function getUserByMail($mail)
    {
        $rowset = $this->select(array('email' => $mail));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find user with mail: $mail");
        }
        return $row;
    }
    public function getUserDetails($where = array(), $columns = array())
    {
        try {
            $sql = new Sql($this->getAdapter());
            $select = $sql->select()->from(array(
                'user' => $this->table
            ));
            
            if (count($where) > 0) {
                $select->where($where);
            }
            
            if (count($columns) > 0) {
                $select->columns($columns);
            }
            $select->join(array('userRole' => 'user_role'), 'userRole.user_id = user.user_id', array('role_id'), 'LEFT');
            $select->join(array('role' => 'role'), 'userRole.role_id = role.rid', array('role_name'), 'LEFT');
            
            $statement = $sql->prepareStatementForSqlObject($select);
            $users = $this->resultSetPrototype->initialize($statement->execute());//->toArray();
            return $users;
        } catch (\Exception $e) {
            throw new \Exception($e->getPrevious()->getMessage());
        }
    }   
    public function saveUser(User $user)
    {
        $data = array(
            'email' => $user->email,
            'name' => $user->name,
            'password'  => $user->password,
        );

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
    }
}
