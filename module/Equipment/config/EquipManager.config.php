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
                    'Zeltverwaltung' => '/equip/' .\Equipment\Model\EnumEquipTypes::TRANSLATE_TO_STRING[\Equipment\Model\EnumEquipTypes::TENT],
                    'Zeugverwaltung' => '/equip/' .\Equipment\Model\EnumEquipTypes::TRANSLATE_TO_STRING[\Equipment\Model\EnumEquipTypes::EQUIPMENT],
                    'Lagerplanung'   => '/siteplan',
                )
            ),
        ),
        'type' => array(
            'name'  => 'type',
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
                    \Equipment\Model\EnumEquipTypes::TENT => \Equipment\Form\TentForm::class,
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
                    \Equipment\Model\EnumEquipTypes::TENT => \Equipment\Form\TentForm::class,
                ),
                'model' => array(
                    \Equipment\Model\EnumEquipTypes::TENT => \Equipment\Model\Tent::class,
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