<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 21.11.2016
 * Time: 17:06
 */

namespace Usermanager\DataTable;

//cleanfix whole file

use Application\DataTable\DataTableHelper;

/**
 * @deprecated ever in use??
 * Class ProfileDataTable
 * @package Usermanager\DataTable
 */
class ProfileDataTable extends DataTable
{
    function __constructor() {
        $this->add(array(
            'name' => 'Name',
            'key' => 'name'
        ));
        bdump ('deprecated: Usermanager\DataTable\ProfileDataTable');
    }
}