<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 13.04.2017
 * Time: 02:24
 */

namespace Application\Model;

use Application\Model\DataObjects\ActiveUsersSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;


// structure:  table: system_log ( sid [string], ip [string], user_id [int], last_action_url[string], time [bigint], data [string|array|object] )
class ActiveUsersTable extends AbstractTableGateway
{
    public $table = 'active_users';
    private $keepAlive;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }
    public function updateActiveUsers($data, $keepAlive) {
        $leaseBreakpoint = $data['time']-$keepAlive;
        $this->keepAlive = $keepAlive;
        $prepare = $this->prepareData($data);
        $queryItems = $prepare[0];
        $queryValues = $prepare[1];
        $query = "REPLACE INTO $this->table ($queryItems) VALUES ($queryValues);
                      DELETE FROM active_users WHERE time < $leaseBreakpoint;";
        $this->adapter->query($query, array());
    }

    public function getActiveUsers()
    {
        return new ActiveUsersSet($this->getWhere());
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

            if ($key == 'data'){
                $value = json_encode($value);
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
//            bdump($select->getSqlString());
            $results = $this->selectWith($select);
            return $results;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}