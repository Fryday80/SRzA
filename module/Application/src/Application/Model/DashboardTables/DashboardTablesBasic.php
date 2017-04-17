<?php

namespace Application\Model\DashboardTables;


use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;

class DashboardTablesBasic extends AbstractTableGateway
{
    public $table;
    protected $configLoad = false;

    public function __construct(Adapter $adapter)
    {
        $this->getConfiguration();
        if($this->configLoad) {
            $this->adapter = $adapter;
            $this->initialize();
        }
    }
    protected function getWhere($where = array(), $columns = array())
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
    
    protected function getConfiguration(){
        //@todo get the configs ->->->$this->configLoad = true;
    }
}