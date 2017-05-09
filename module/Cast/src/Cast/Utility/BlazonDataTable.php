<?php
namespace Cast\Utility;

use Application\Utility\DataTable;

class BlazonDataTable extends DataTable
{
    function __construct() {
        parent::__construct();

        $this->add(array(
            'name' => 'Wappen',
            'type' => 'custom',
            'render' => function($row) {
                $links = '<img height="50px" src="/media/file/wappen/' . $row['filename'] . '">';
                return $links;
            }
        ));
        $this->add(array(
            'name' => 'BigWappen',
            'type' => 'custom',
            'render' => function($row) {
                $pic = (isset($row['bigFilename']))? '/media/file/wappen/'.$row['bigFilename'] : '/img/uikit/cross-NO.gif';
                $links = '<img height="50px" src="' . $pic . '">';
                return $links;
            }
        ));
        $this->add(array(
            'name' => 'name',
            'label' => 'Name',
        ));
        $this->add(array(
            'name' => 'Aktionen',
            'type' => 'custom',
            'render' => function($row) {
                $links = '<a href="/castmanager/wappen/edit/' . $row['id'] . '">Bearbeiten</a>';
                $links .= ' <a href="/castmanager/wappen/delete/' . $row['id'] . '">Löschen</a>';
                return $links;
            }
        ));
    }
}