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

    public function getUserByName($username)
    {
        return $this->getUsersBy('name', $username);
    }
    
    private function getUsersBy($columnName, $value) {
        $rowset = $this->getUsersWhere(array($columnName => $value));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find user with $columnName: $value");
        }
        return $row;
    }

    /**
     * @param $id
     * @return User|bool
     */
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
        $data = $user->getArrayCopy();
        unset($data['role_name']);
        if (isset($data['activeUser'])) unset($data['activeUser']);
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
                if ($data['password'] == null) unset($data['password']);
                if ($data['user_image'] == null) unset($data['user_image']);
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

    /** Get users by params without password
     * @param array $where
     * @return ResultSetInterface
     * @throws \Exception
     */
    private function getUsersWhere($where = array())
    {

        try {
            $sql = $this->getSql();
            $select = $sql->select();

            if (count($where) > 0) {
                $select->where($where);
            }
            $select->columns(array(
                'id',
                'email',
                'name',
                'status',
                'role_id',
                'created_on',
                'modified_on',
                'street',
                'city',
                'zip',
                'member_number',
                'real_name',
                'real_surename',
                'birthday',
                'gender',
                'user_image',
            ));
            $select->join(array('role' => 'role'), 'users.role_id = role.rid', array('role_name'), 'LEFT');
            $users = $this->selectWith($select);

            return $users;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
