<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 13.04.2017
 * Time: 02:25
 */

namespace Application\Model;

use Application\DataObjects\SystemLogSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;


class SystemLogTable extends AbstractTableGateway
{
// structure:  table: system_log (id, type [string], title [string], message [string], time [bigint], data [string|array|object] )
    public $table = 'system_log';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    /**
     * @param array $data array with data type[string], title[string], message[string], time, data[string|array|object]
     * @return mixed
     */
    public function updateSystemLog($data)
    {
        $prepare = $this->prepareData($data);
        if ($prepare == NULL)return  trigger_error( "data failure", E_USER_ERROR );
        $queryItems = $prepare[0];
        $queryValues = $prepare[1];
        $query = "INSERT INTO $this->table ($queryItems) VALUES ($queryValues);";
        $this->adapter->query($query, array());
    }

    public function getSystemLogs ($since = null)
    {
        // @todo check sorting of result array is to be new to old
//        $data = array_reverse( $this->getWhere()->toArray() );
        $data = $this->getWhere()->toArray();
//        bdump( $data );
        if ($since !== null && is_int($since))
        {
            $newDataSet = array();
            for ($i = 0; $i < count($data); $i++)
            {
                if ($data[$i]->time < $since) return new SystemLogSet($newDataSet);
                $newDataSet[$i] = $data[$i];
            }
        }
        return new SystemLogSet($data);
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
            $value = ($key == 'data') ? json_encode($value) : $value;
            $queryValues .= (is_int($value)) ? $value. ", " : "'$value', ";
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
