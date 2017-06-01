<?php
namespace Cast\Utility;


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
                if (is_object( $row )){
                    $links = '<a href="/Cast/showprofile/' . $row->id . '">Auswählen</a>';
                    if (array_key_exists('allow_delete', $row)) {
                        $links .= '<a href="/Cast/delete/' . $row->id . '">Löschen</a>';
                    }
                    return $links;

                } else {
                    $links = '<a href="/Cast/showprofile/' . $row['id'] . '">Auswählen</a>';
                    if (array_key_exists('allow_delete', $row)) {
                        $links .= '<a href="/Cast/delete/' . $row['id'] . '">Löschen</a>';
                    }
                    return $links;
                }
            }
        ));
    }
}