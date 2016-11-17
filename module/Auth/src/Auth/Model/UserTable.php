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
            throw new \Exception("Could not find user with id: $id");
        }
        return $row;
    }
    public function getUserByMail($mail)
    {
        $rowset = $this->getUsersWhere(array('users.email' => $mail));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find user with mail: $mail");
        }
        return $row;
    }
    public function saveUser(User $user)
    {

        $data = array(
            'email' => $user->email,
            'name' => $user->name,
            'password'  => $user->password,
            'sure_name' => $user->sureName,
            'gender' => $user->gender,
            'vita' => $user->vita,
            'family_id' => $user->familyID,
            'family_order' => $user->familyOrder,
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

    private function getUsersWhere($where = array(), $columns = array())
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

            $select->join(array('userRole' => 'user_role'), 'userRole.user_id = users.id', array('role_id'), 'LEFT');
            $select->join(array('role' => 'role'), 'userRole.role_id = role.rid', array('role_name'), 'LEFT');

            $users = $this->selectWith($select);
            return $users;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
