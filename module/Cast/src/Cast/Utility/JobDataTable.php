<?php
namespace Cast\Utility;

use Application\Utility\DataTable;

class JobDataTable extends DataTable
{
    function __construct() {
        parent::__construct();
        
        $this->addColumn(array(
            'name' => 'job',
            'label' => 'Job',
        ));
        $this->addColumn(array(
            'name' => 'Aktionen',
            'type' => 'custom',
            'render' => function($row) {
                $links = '<a href="/castmanager/jobs/edit/' . $row['id'] . '">Bearbeiten </a>';
                $links .= ' <a href="/castmanager/jobs/delete/' . $row['id'] . '">Löschen</a>';
                return $links;
            }
        ));
    }
}