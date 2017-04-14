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

class ActiveUsersTable extends AbstractTableGateway
{
    public $table = 'active_users';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }
    public function updateActive($data, $storeDuration) {
        $leaseTime = $data['time']-$storeDuration;
        $prepare = $this->prepareData($data);
        $queryItems = $prepare[0];
        $queryValues = $prepare[1];
        $query = "REPLACE INTO $this->table ($queryItems) VALUES ($queryValues);
                      DELETE FROM active_users WHERE time < $leaseTime;";
        $this->adapter->query($query, array());
    }
    public function getActiveUsers(){
        $return = $this->getWhere()->toArray();
        // unserialize serialized data
        foreach ($return as $key => $row ) {
            $return[$key]['action_data'] = unserialize($row['action_data']);
        }
        return $return;
    }

    /** Prepare data for query
     *
     * @param array $data
     * @return array|null [0] = sql columns line up, [1] = the fitting sql VALUES
     */
    private function prepareData($data)
    {
        $queryItems ='';
        $queryValues = '';

        //create SQL items and values line up
        foreach ($data as $key => $value){
            $queryItems .= $key . ", ";

            if ($key == 'action_data'){
                $value = serialize($value);
            }
            if (is_int($value)) {
                $queryValues .= $value. ", ";
            } else {
                $queryValues .= "'$value', ";
            }
        }
        
        $queryItems = substr($queryItems, 0, -2);
        $queryValues = substr($queryValues, 0, -2);

        return array($queryItems, $queryValues);
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