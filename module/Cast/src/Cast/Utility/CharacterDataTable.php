<?php
namespace Cast\Utility;

use Application\Utility\DataTable;

class CharacterDataTable extends DataTable
{
    function __construct() {
        parent::__construct();
        
        $this->addColumn(array(
            'name' => 'name',
            'label' => 'Name',
        ));
        $this->addColumn(array(
            'name' => 'surename',
            'label' => 'Nachname',
        ));
        $this->addColumn(array(
            'name' => 'Aktionen',
            'type' => 'custom',
            'render' => function($row) {
                $links = '<a href="/castmanager/characters/edit/' . $row['id'] . '">Bearbeiten</a>';
                $links .= ' <a href="/castmanager/characters/delete/' . $row['id'] . '">Löschen</a>';
                return $links;
            }
        ));
    }
}