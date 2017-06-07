<?php
namespace Auth\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
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
            $select->join(array('role' => 'role'), 'users.role_id = role.rid', array('role_name'), 'LEFT');
            $user = $this->selectWith($select)->current();
            if (!$user) {
                return null;
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
        $data['modified_on'] = time();
        $id = (int) $user->id;
        if ($id == 0) {
            //new user
            $data['status'] = (isset($data['status'])) ? $data['status'] : 0;
            $data['created_on'] = $data['modified_on'];
            $this->insert($data);
        } else {
            //edit user
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

    /**
     * @param array $where
     * @param array $columns
     * @return ResultSetInterface
     * @throws \Exception
     */
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
