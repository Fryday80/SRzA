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

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }
    public function updateActive($data, $storeTime) {
        $leaseTime = $data['last_action_time']-$storeTime;
        $prepare = $this->prepareData($data);
        if ($prepare == NULL)return; //@todo error msg "data missing"
        $queryItems = $prepare[0];
        $queryValues = $prepare[1];
        $query = "REPLACE INTO active_users ($queryItems)
                      VALUES ($queryValues);
                      DELETE FROM active_users WHERE last_action_time < $leaseTime;";
        $this->adapter->query($query, array());
    }
    public function getActiveUsers(){
        $return = $this->getWhere()->toArray();
        // unserialize serialized data
        foreach ($return as $key => $row ) {
            $imploded = unserialize($row['serialized_columns']);
            foreach ($imploded as $valueKey){
                $return[$key][$valueKey] = unserialize($row[$valueKey]);
            }
            // remove no longer used data
            unset ($return[$key]['serialized_columns']);
        }
        return $return;
    }

    /** Prepare data and check for required table columns
     *
     * @param array $data
     * @return array|null [0] = sql columns line up, [1] = the fitting sql VALUES
     */
    private function prepareData($data){
        $requiredColumns = array(
            'ip' => false,
            'sid' => false,
            'user_id' => false,
            'last_action_time' => false,
            'last_action_url' => false
        );
        $serializedColumns = array();
        $queryItems ='';
        $queryValues = '';

        //create SQL items and values line up
        foreach ($data as $key => $value){
            if (array_key_exists( $key, $requiredColumns )){
                $requiredColumns[$key] = true;
            }
            if (is_array($value)){
                $value = serialize($value);
                array_push($serializedColumns, "'" . $key . "'");
            }
            $queryItems .= $key . ", ";
            if (is_int($value)) {
                $queryValues .= $value. ", ";
            } else {
                $queryValues .= "'$value', ";
            }
        }
        $queryItems .= 'serialized_columns';
        $queryValues .= serialize($serializedColumns);

        // all required given?
        if (in_array(false, $requiredColumns))return NULL;
        // then:
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