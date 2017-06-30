<?php
//======= "in module links":
$links = array(
    'zurück zur Managerübersicht' => '/equip',
);



return array(
    'config' => array(
        'index' => array(
            'name'    => 'index',
            'label'   => 'Lager',
            'vars'    => array(
                'links' => array(
                    'Zeltverwaltung' => '/equip/tent',
                    'Zeugverwaltung' => '/equip/equipment',
                    'Lagerplanung'   => '/siteplan',
                )
            ),
        ),
        'type' => array(
            'name'  => 'tent',
            'label' => 'Lager',
            'vars'  => array(
                'links' => $links,
            ),
        ),
        'add'  => array(
            'name'  => 'add',
            'label' => 'Neu',
            'vars' => array(
                'links' => $links,
                'site' => 'add',
                'formType' => array(
                    'tent' => \Equipment\Form\TentForm::class,
                ),
                'model' => array(
                    'tent' => \Equipment\Model\Tent::class,
                ),
            ),
        ),
        'userall'  => array(
            'name'  => 'userall',
            'label' => 'Alle Zelte',
            'vars' => array(
                'links' => $links,
            ),
        ),
        'show' => array(
            'name' => 'show',
            'label' => 'Details',
            'vars' => array(
                'links' => $links,
            ),
        ),
        'delete'   => array(
            'name' => 'delete',
            'label' => 'Details',
            'vars' => array(
                'links' => $links,
            ),
        ),
        'edit' => array(
            'name' => 'edit',
            'label' => 'Details',
            'vars' => array(
                'links' => $links,
                'formType' => array(
                    'tent' => \Equipment\Form\TentForm::class,
                ),
                'model' => array(
                    'tent' => \Equipment\Model\Tent::class,
                ),
            ),
        ),
//        'edittenttypes'   => array(
//            'name' => 'edittenttypes',
//            'label' => 'edittenttypes',
//            'vars' => array(
//                'links' => $links,
//            ),
//        ),
    ),
    'functions' => array(
        'getVars' => function($action, $config){
            return $config['config'][$action]['vars'];
        },
        'getPageConfig' => function($action, $config){
            return $config['config'][$action];
        },
    ),
);