<?php
/**
 * Created by PhpStorm.
 * User: salt
 * Date: 28.11.2016
 * Time: 18:45
 */

namespace Usermanager\Utility;


use Application\Utility\DataTable;

class UserDataTable extends DataTable
{
    function __construct() {
        parent::__construct($delete);
        
        $this->add(array(
            'name' => 'Name',
            'type' => 'text',
            'dataIndex' => 'name',
        ));
        $this->add(array(
            'name' => 'eMail',
            'type' => 'text',
            'dataIndex' => 'email',
        ));
        $this->add(array(
            'name' => 'Aktionen',
            'type' => 'custom',
            'render' => function($row) {
                $links = '<a href="/usermanager/profile/' . $row['id'] . '">Auswählen</a>';
                if (array_key_exists('allow_delete', $row)) {
                    $links .= '<a href="/usermanager/delete/' . $row['id'] . '">Löschen</a>';
                }
                return $links;
            }
        ));
    }
}