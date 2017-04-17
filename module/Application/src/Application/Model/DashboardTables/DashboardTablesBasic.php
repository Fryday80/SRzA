<?php
//
//namespace Application\Model\DashboardTables;
//
//
//use Zend\Db\TableGateway\AbstractTableGateway;
//
//class DashboardTablesBasic extends AbstractTableGateway
//{
//    protected $config;
//    protected $columnsConfig;
//    protected $sort = array();
//    protected $serialize;
//    protected $required;
//    protected $isInt;
//    protected $isString;
//
//    function __construct($adapter)
//    {
//        $this->adapter = $adapter;
//        $this->initialize();
//        $this->getConfig();
//    }
//
//    protected function getWhere($where = array(), $columns = array())
//    {
//        try {
//            $sql = $this->getSql();
//            $select = $sql->select();
//
//            if (count($where) > 0) {
//                $select->where($where);
//            }
//            if (count($columns) > 0) {
//                $select->columns($columns);
//            }
////            $select->join(array(
////                'parent' => $this->table
////            ),
////                'parent.rid = role.role_parent', array('role_parent_name' => 'role_name'), 'left'
////            );
//
//            $results = $this->selectWith($select);
//            return $results;
//        } catch (\Exception $e) {
//            throw new \Exception($e->getMessage());
//        }
//    }
//
//    protected function getConfig()
//    {
//        $this->config = $this->configFileImport();
//        $this->setConfigFromVar();
//    }
//
//    /**
//     * only last sort = tru will be used!
//     */
//    protected function setConfigFromVar()
//    {
//        $this->table = $this->config['db']['table'];
//        $this->columnsConfig = $this->config['db']['columns'];
//        foreach ($this->columnsConfig as $column_name => $column_data)
//        {
//            foreach ($column_data as $key => $value){
//                $this->sort = ($column_data['sort'] == true) ? $column_name : $this->sort;
//                if ($column_data['required']['isRequired'] == true)
//                {
//                   $this->required[$column_name] = ( isset($column_data['required']['default']) ) ? $column_data['required']['default'] : 'site default';
//                }
//                if ($column_data['required'] == true)
//                {
//                    array_push($this->serialize, $column_name);
//                }
//                if ($column_data['input_type'] = 'int') {
//                    array_push($this->isInt, $column_name);
//                } else {
//                    array_push($this->isString, $column_name);
//                }
//            }
//        }
//    }
//
//    protected function configFileImport()
//    {
//        // @todo import config file
////
////        example
////          return array(
////            'db' => array(
////                'columns' => array(
////                    'col_name' => array(
////                        'required' => array(
////                          'value' => true,
////                          'default' => 'default Value',
////                        ),
////                        'serialized' => false,
////                        'input_type' => 'string or int or all',
////                        'sort' => false,  //if true this is the key that is the sort criteria, only the last is used
////                    ),
////                ),
////                'table' => 'table_name',
////            ),
////        );
//    }
//
//}