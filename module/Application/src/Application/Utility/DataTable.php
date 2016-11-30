<?php
/**
 * Created by PhpStorm.
 * User: salt
 * Date: 28.11.2016
 * Time: 18:39
 */

namespace Application\Utility;


class DataTable
{
    public $data;
    public $columns;

    function __construct() {
        $this->columns = array();
    }
    public function add($columnConf) {
        //@todo validate $columnConf
        array_push($this->columns, $columnConf);
    }
    public function setData($data) {
        //@todo validate $data
        $this->data = $data;
    }
}