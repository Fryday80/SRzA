<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 21.11.2016
 * Time: 17:06
 */

namespace Usermanager\DataTable;


use Application\DataTable\DataTableHelper;

class ProfileDataTable extends DataTable
{
    function __constructor() {
        $this->add(array(
            'name' => 'Name',
            'key' => 'name'
        ));
    }
}