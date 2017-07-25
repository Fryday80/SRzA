<?php
use Application\Factory\Basic\DefaultTableGatewayFactory;

return array(
	'Equipment_ImageProcessor' => array(
		'images' => array(
			'x' => 500,
			'y' => 500
		),
		'storage_path' => array(
			'base' => '', //use imageProcessors base path
			'folder_structure' => array(
				'_equipment',
				'{{id}}'
			),
			'override_names' => array(
				'image1' => 'image1',
				'image2' => 'image2'
			),
		),
	),
    'Equipment' => array(
        'config' => array(
            'default_actionName' => array(
                'name' => 'actionName',
                'label'=> 'label',
                'vars' => array(
					'links' => array(
						'Zeltverwaltung' => '/equip/' .\Equipment\Model\Enums\EEquipTypes::TRANSLATE_TO_STRING[\Equipment\Model\Enums\EEquipTypes::TENT],
						'Zeugverwaltung' => '/equip/' .\Equipment\Model\Enums\EEquipTypes::TRANSLATE_TO_STRING[\Equipment\Model\Enums\EEquipTypes::EQUIPMENT],
						'Lagerplanung'   => '/siteplanner',
					)
				),
            ),
            'index'   => array(
                'name'    => 'index',
                'label'   => 'Lager',
            ),
            'type'    => array(
                'name'  => 'type',
                'label' => 'Lager',
                'vars'  => array(
                    'links' => array(
                        'zurück zur Managerübersicht' => '/equip',
                    ),
                ),
            ),
            'add'     => array(
                'name'  => 'add',
                'label' => array(
                    \Equipment\Model\Enums\EEquipTypes::TENT => 'Neues Zelt anlegen',
                    \Equipment\Model\Enums\EEquipTypes::EQUIPMENT => 'Neuen Ausrüstungsgegenstand anlegen',
                    ),
                'vars' => array(
                    'links' => array(
                        'zurück zur Managerübersicht' => '/equip',
                    ),
                    'site' => 'add',
                    'formType' => array(
                        \Equipment\Model\Enums\EEquipTypes::TENT => \Equipment\Form\TentForm::class,
                        \Equipment\Model\Enums\EEquipTypes::EQUIPMENT => \Equipment\Form\EquipmentForm::class
                    ),
                    'model' => array(
                        \Equipment\Model\Enums\EEquipTypes::TENT => \Equipment\Model\DataModels\Tent::class,
                        \Equipment\Model\Enums\EEquipTypes::EQUIPMENT => \Equipment\Model\DataModels\Equip::class
                    ),
                ),
            ),
            'userall' => array(
                'name'  => 'userall',
                'label' => array(
                    \Equipment\Model\Enums\EEquipTypes::TENT => 'Alle Zelte',
                    \Equipment\Model\Enums\EEquipTypes::EQUIPMENT => 'Equipment'
                ),
                'vars' => array(
                    'links' => array(
                        'zurück zur Managerübersicht' => '/equip',
                    ),
                ),
            ),
            'show'    => array(
                'name' => 'show',
                'label' => 'Details',
                'vars' => array(
                    'links' => array(
                        'zurück zur Managerübersicht' => '/equip',
                    ),
                ),
            ),
            'delete'  => array(
                'name' => 'delete',
                'label' => 'Details',
                'vars' => array(
                    'links' => array(
                        'zurück zur Managerübersicht' => '/equip',
                    ),
                ),
            ),
            'edit'    => array(
                'name' => 'edit',
                'label' => 'Details',
                'vars' => array(
                    'links' => array(
                        'zurück zur Managerübersicht' => '/equip',
                    ),
                    'formType' => array(
                        \Equipment\Model\Enums\EEquipTypes::TENT => \Equipment\Form\TentForm::class,
                        \Equipment\Model\Enums\EEquipTypes::EQUIPMENT => \Equipment\Form\EquipmentForm::class
                    ),
                    'model' => array(
                        \Equipment\Model\Enums\EEquipTypes::TENT => \Equipment\Model\DataModels\Tent::class,
                        \Equipment\Model\Enums\EEquipTypes::EQUIPMENT => \Equipment\Model\DataModels\Equip::class
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array( ),
        'factories' => array(
            'Equipment\Controller\SitePlanner' => 'Equipment\Factory\Controller\SitePlannerControllerFactory',
            'Equipment\Controller\Equipment'   => 'Equipment\Factory\Controller\EquipmentControllerFactory',
        ),
    ),
    'view_helpers' => array(
        'factories' => array( ),
        'invokables' => array( )
    ),
    'service_manager' => array(
        'factories' => array(
            'EquipmentService' => 'Equipment\Factory\Service\EquipmentServiceFactory',
        ),
        'abstract_factories' => array(
            'Equipment\Model\TentTypesTable' => DefaultTableGatewayFactory::class,
            'Equipment\Model\Tables\SitePlannerTable' => DefaultTableGatewayFactory::class,
            'Equipment\Model\Tables\EquipTable' => DefaultTableGatewayFactory::class,
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Equipment' => __DIR__ . '/../view',
        ),
    ),
     'router' => array(
         'routes' => array(
             'equipmanager' => array(
                 'type'    => 'segment',
                 'may_terminate' => true,
                 'options' => array(
                     'route'    => '/equip',
                     'defaults' => array(
                         'controller' => 'Equipment\Controller\Equipment',
                         'action'     => 'index',
                     ),
                 ),
                 'child_routes' => array(
                     // /equip/:type
                     'type' => array(
                         'type'    => 'segment',
                         'may_terminate' => true,
                         'options' => array(
                             'route'    => '/:type',
                             'constraints' => array(
                                 'type' => '[a-z]+'
                             ),
                             'defaults' => array(
                                 'controller' => 'Equipment\Controller\Equipment',
                                 'action'     => 'type',
                             ),
                         ),
                         //children
                         'child_routes' => array(
                             // /equip/:type/add
                             'add' => array(
                                 'type' => 'Segment',
                                 'options' => array(
                                     'route' => '/add[/:userId]',
                                     'constraints' => array(
                                         'userId' => '[0-9]+'
                                     ),
                                     'defaults' => array(
                                         'controller' => 'Equipment\Controller\Equipment',
                                         'action' => 'add'
                                     )
                                 ),
                                 'may_terminate' => true,
                             ),
                             // /equip/:type/:userId
                             'user_all' => array(
                                 'type' => 'Segment',
                                 'options' => array(
                                     'route'    => '/:userId',
                                     'constraints' => array(
                                         'userId' => '[0-9]+'
                                     ),
                                     'defaults' => array(
                                         'controller' => 'Equipment\Controller\Equipment',
                                         'action'     => 'userall',
                                     ),
                                 ),
                                 'may_terminate' => true,
                                 'child_routes' => array(
                                     // /equip/:type/:userId/show/:equipId
                                     'user_show' => array(
                                         'type' => 'Segment',
                                         'options' => array(
                                             'route'    => '/show/:equipId',
                                             'constraints' => array(
                                                 'tentId' => '[0-9]+'
                                             ),
                                             'defaults' => array(
                                                 'controller' => 'Equipment\Controller\Equipment',
                                                 'action'     => 'show',
                                             ),
                                         ),
                                         'may_terminate' => true,
                                     ),
                                     // /equip/:type/:userId/delete/:equipId
                                     'delete' => array(
                                         'type' => 'Segment',
                                         'options' => array(
                                             'route' => '/delete/:equipId',
                                             'constraints' => array(
                                                 'tentId' => '[0-9]+'
                                             ),
                                             'defaults' => array(
                                                 'action' => 'delete'
                                             )
                                         ),
                                         'may_terminate' => true,
                                     ),
                                     // /equip/:type/:userId/edit/:equipId
                                     'edit' => array(
                                         'type' => 'Segment',
                                         'options' => array(
                                             'route' => '/edit/:equipId',
                                             'constraints' => array(
                                                 'tentId' => '[0-9]+'
                                             ),
                                             'defaults' => array(
                                                 'action' => 'edit'
                                             )
                                         ),
                                         'may_terminate' => true,
                                     ),
                                 )
                             )
                         ),
                     ),
                 ),
             ),
             'site_planner' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/siteplanner',
                     'defaults' => array(
                         'controller' => 'Equipment\Controller\SitePlanner',
                         'action'     => 'index',
                     ),
                 ),
                 'may_terminate' => true,
                 'child_routes' => array(
                     'list' => array(
                         'type'    => 'segment',
                         'may_terminate' => true,
                         'options' => array(
                             'route'    => '/list',
                             'defaults' => array(
                                 'controller' => 'Equipment\Controller\SitePlanner',
                                 'action'     => 'list',
                             )
                         )
                     ),
                     'get' => array(
                         'type'    => 'segment',
                         'may_terminate' => true,
                         'options' => array(
                             'route'    => '/get/:id',
                             'constraints' => array(
                                 'id' => '[0-9]+'
                             ),
                             'defaults' => array(
                                 'controller' => 'Equipment\Controller\SitePlanner',
                                 'action'     => 'get',
                             )
                         )
                     ),
                     'save' => array(
                         'type'    => 'segment',
                         'may_terminate' => true,
                         'options' => array(
                             'route'    => '/save',
                             'defaults' => array(
                                 'controller' => 'Equipment\Controller\SitePlanner',
                                 'action'     => 'save',
                             )
                         )
                     ),
                     'imageUpload' => array(
                         'type'    => 'segment',
                         'may_terminate' => true,
                         'options' => array(
                             'route'    => '/imageUpload',
                             'defaults' => array(
                                 'controller' => 'Equipment\Controller\SitePlanner',
                                 'action'     => 'imageUpload',
                             )
                         )
                     ),
                     'delete' => array(
                         'type'    => 'segment',
                         'may_terminate' => true,
                         'options' => array(
                             'route'    => '/delete/:id',
                             'constraints' => array(
                                 'id' => '[0-9]+'
                             ),
                             'defaults' => array(
                                 'controller' => 'Equipment\Controller\SitePlanner',
                                 'action'     => 'delete',
                             )
                         )
                     ),
                 ),
             ),
         ),
     ),
);