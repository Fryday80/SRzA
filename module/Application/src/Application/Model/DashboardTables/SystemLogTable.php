<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 13.04.2017
 * Time: 02:25
 */

namespace Application\Model\DashboardTables;

use Application\Model\DashboardTables\DashboardTablesBasic;
use Application\Model\DataObjects\SystemLogSet;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;


// structure:  table: system_log (id, type [string], title [string], message [string], time [bigint], data [string|array|object] )
class SystemLogTable extends DashboardTablesBasic
{
    public $table = 'system_log';

    protected $config;
    protected $columnsConfig;
    protected $sort;
    protected $serialized = array();
    protected $required = array();
    protected $isInt = array();
    protected $isString = array();
    

    public function __construct(Adapter $adapter)
    {
        if($this->configLoad) parent::__construct($adapter);
        else {
            $this->getConfig();
            $this->adapter = $adapter;
            $this->initialize();
        }
    }

    /**
     * @param array $data array with data type[string], title[string], message[string], time, data[string|array|object]
     * @return mixed
     */
    public function updateSystemLog($data)
    {
        $prepare = $this->prepareDataForInsertQuery($data);
        if ($prepare == NULL)return  trigger_error( "data failure", E_USER_ERROR );
        $queryItems = $prepare[0];
        $queryValues = $prepare[1];
        $query = "INSERT INTO $this->table ($queryItems) VALUES ($queryValues);";

        $this->adapter->query($query, array());
    }

    public function getSystemLogs ($since = null)
    {
        $query = "SELECT * FROM $this->table ORDER BY `time` DESC;";
        $data = $this->adapter->query($query, array());

        return new SystemLogSet($data, $since);
    }
    
    /** Prepare data for query
     *
     * @param array $data
     * @return array|null [0] = sql columns line up, [1] = the fitting sql VALUES
     */
    private function prepareDataForInsertQuery($data)
    {
        $queryItems ='';
        $queryValues = '';

        //create SQL items and values line up
        foreach ($data as $key => $value){
            $queryItems .= $key . ", ";
            $value = ( in_array($key, $this->serialized) ) ? json_encode($value) : $value;
            $queryValues .= (is_int($value)) ? $value. ", " : "'$value', ";
        }
        $queryItems = substr($queryItems, 0, -2);
        $queryValues = substr($queryValues, 0, -2);

        return array($queryItems, $queryValues);
    }
    protected function getConfig()
    {
        $this->config = $this->configFileImport();
        $this->setConfigFromVar();
    }

    /**
     * only last sort = tru will be used!
     */
    protected function setConfigFromVar()
    {
        $this->table = $this->config['db']['table'];
        $this->columnsConfig = $this->config['db']['columns'];
        foreach ($this->columnsConfig as $column_name => $column_data)
        {
            foreach ($column_data as $key => $value){
                $this->sort = ($column_data['sort'] == true) ? $column_name : $this->sort;
                if ($column_data['required']['isRequired'] == true)
                {
                    $this->required[$column_name] = ( isset($column_data['required']['default']) ) ? $column_data['required']['default'] : 'site default';
                }
                if ($column_data['serialized'] == true)
                {
                    array_push($this->serialized, $column_name);
                }
                if ($column_data['input_type'] = 'int') {
                    array_push($this->isInt, $column_name);
                } else {
                    array_push($this->isString, $column_name);
                }
            }
        }
    }

    protected function configFileImport()
    {
        return array(
            'db' => array(
                'columns' => array(
                    'type' => array(
                        'required' => array(
                            'isRequired' => true,
                            'default' => 'default Value',
                        ),
                        'serialized' => false,
                        'input_type' => 'string',
                        'sort' => false,
                    ),
                    'title' => array(
                        'required' => array(
                            'isRequired' => true,
                            'default' => 'default Value',
                        ),
                        'serialized' => false,
                        'input_type' => 'string',
                        'sort' => false,
                    ),
                    'message' => array(
                        'required' => array(
                            'isRequired' => true,
                            'default' => 'default Value',
                        ),
                        'serialized' => false,
                        'input_type' => 'string',
                        'sort' => false,
                    ),
                    'time' => array(
                        'required' => array(
                            'isRequired' => true,
                            'default' => 42,
                        ),
                        'serialized' => false,
                        'input_type' => 'int',
                        'sort' => true,
                    ),
                    'data' => array(
                        'required' => array(
                            'isRequired' => true,
                            'default' => 'default Value',
                        ),
                        'serialized' => true,
                        'input_type' => 'all',
                        'sort' => false,
                    ),
                ),
                'table' => 'system_log',
            ),
        );
    }
}
