<?php
$data = array(
    'data' => array (
        0 => array ( 'id'=> '1', 'name'=>'families ('.count($families).')', 'link' => 'families' ),
        1 => array ( 'id'=> '2', 'name'=>'jobs ('.count($jobs).')', 'link' => 'jobs' ),
        2 => array ( 'id'=> '3', 'name'=>'characters ('.count($characters).')', 'link' => 'characters' )
    ),
    'columns' =>    array(
        array (
            'name'  => 'name',
            'label' => 'Gruppen'
        ),
        array (
            'name'  => 'href',
            'label' => 'Gruppen',
            'type'  => 'custom',
            'render' => function($row) {
                $edit = '<a href="/castmanager/'.$row['link'].'">'.$row['name'].'</a>';
                return $edit;
            }
        )
    ),
    'jsConfig' => array(
        'buttons' => array(
            'pdf',
            'print',
            "csv",
            "excel",
            array(
                'text' => 'add resource',
                'url' => '/resource/add',
            )
        ),
        'dom' => array(
            'B' => true,
        )
    ),
);
?>