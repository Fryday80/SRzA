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
        parent::__construct();
        
        $this->add(array(
            'name' => 'name',
            'label' => 'Name',
        ));
        $this->add(array(
            'name' => 'email',
            'label' => 'eMail',
        ));
        $this->add(array(
            'name' => 'Aktionen',
            'type' => 'custom',
            'render' => function($row) {
                $links = '<a href="/usermanager/showprofile/' . $row['id'] . '">Auswählen</a>';
                if (array_key_exists('allow_delete', $row)) {
                    $links .= '<a href="/usermanager/delete/' . $row['id'] . '">Löschen</a>';
                }
                return $links;
            }
        ));
    }
}