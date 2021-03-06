<?php

namespace Application\Model\Tables;

use Application\Model\DataModels\SystemLog;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;

class SystemLogTable extends AbstractTableGateway
{
    public $table = 'system_log';


    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
        $this->initialize();
    }

    /**
     * @param SystemLog $logItem
     * @return mixed
     */
    public function updateSystemLog(SystemLog $logItem)
    {
        $prepare = $this->prepareDataForInsertQuery($logItem);
        if ($prepare == NULL)return trigger_error( "data failure", E_USER_ERROR );
        $queryItems = $prepare[0];
        $queryValues = $prepare[1];
        $query = "INSERT INTO $this->table ($queryItems) VALUES ($queryValues);";
		bdump($query);

        $res = $this->adapter->query($query, array());
    }

    /**
     * @return array array of SystemLogs    SystemLog[]
     */
    public function getSystemLogs ()
    {
        $query = "SELECT * FROM $this->table ORDER BY `microtime` DESC;";
        $data = $this->adapter->query($query, array());
        $result = array();
        foreach ($data as $row){
            array_push($result, new SystemLog($row->microtime, $row->type, $row->msg, $row->url, $row->userId, $row->userName, json_decode($row->data) ) );
        }

        return $result;
    }

    /** Prepare data for query
     *
     * @param SystemLog $data
     * @return array|null [0] = sql columns line up, [1] = the fitting sql VALUES
     */
    private function prepareDataForInsertQuery(SystemLog $data)
    {
        $queryItems ='';
        $queryValues = '';
        //create SQL items and values line up
        foreach ($data as $key => $value){
                $queryItems .= $key . ", ";
            if ($key == 'data'){
                $value = json_encode($value);
            }
            $queryValues .= (is_int($value) || is_float($value)) ? $value. ", " : "'$value', ";
        }
        $queryItems = substr($queryItems, 0, -2);
        $queryValues = substr($queryValues, 0, -2);
        return array($queryItems, $queryValues);
    }
}
