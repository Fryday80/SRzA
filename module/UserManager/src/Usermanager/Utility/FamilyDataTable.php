<?php
namespace Usermanager\Utility;

use Application\Utility\DataTable;

class FamilyDataTable extends DataTable
{
    function __construct() {
        parent::__construct();
        
        $this->add(array(
            'name' => 'name',
            'label' => 'Name',
        ));
        $this->add(array(
            'name' => 'Aktionen',
            'type' => 'custom',
            'render' => function($row) {
                $links = '<a href="/families/edit/' . $row['id'] . '">Bearbeiten</a>';
                if (array_key_exists('allow_delete', $row)) {
                    $links .= '<a href="/usermanager/delete/' . $row['id'] . '">Löschen</a>';
                }
                return $links;
            }
        ));
    }
}