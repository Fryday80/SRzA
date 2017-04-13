<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 13.04.2017
 * Time: 02:24
 */

namespace Application\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;

class ActiveUsers extends AbstractTableGateway
{

    public $table = 'active_users';
    private $activeUsers;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }
    public function updateActive($data, $storeTime) {
        $lease = $data['last_action_time']-$storeTime;
        $prepare = $this->prepareData($data);
        if ($prepare == NULL)return; //@todo error msg "data missing"
        $sqlItems = $prepare[0];
        $sqlValues = $prepare[1];
        $query = "REPLACE INTO active_users ($sqlItems)
                      VALUES ($sqlValues);
                      DELETE FROM active_users WHERE last_action_time < $lease;";
        $this->adapter->query($query, array());
    }
    public function getActiveUsers(){
        return $this->getWhere();
    }

    private function prepareData($data){
        $required = array(
            'ip' => false,
            'sid' => false,
            'user_id' => false,
            'last_action_time' => false,
            'last_action_url' => false
        );
        $sqlItems ='';
        $sqlValues = '';

        foreach ($data as $key => $value){
            if (array_key_exists( $key, $required )){
                $required[$key] = true;
            }
            $sqlItems .= $key . ", ";
            if (is_int($value)) {
                $sqlValues .= $value. ", ";
            } else {
                $sqlValues .= "'$value', ";
            }
        }
        $sqlItems = substr($sqlItems, 0, -2);
        $sqlValues = substr($sqlValues, 0, -2);
        if (in_array(false, $required))return NULL;
        return array($sqlItems, $sqlValues);
    }
    private function getWhere($where = array(), $columns = array())
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
//            $select->join(array(
//                'parent' => $this->table
//            ),
//                'parent.rid = role.role_parent', array('role_parent_name' => 'role_name'), 'left'
//            );
            
            $results = $this->selectWith($select);
            return $results;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}