<?php
use Application\Factory\DefaultTableGatewayFactory;

return array(
	'Cast_MediaService' => array(
		'blazon' => array (
			'maxSide' => 400,
		),
	),
    'controllers' => array(
        'invokables' => array(
        ),
        'factories' => array(
            'Cast\Controller\Blazon'        => 'Cast\Factory\Controller\BlazonControllerFactory',
            'Cast\Controller\Cast'          => 'Cast\Factory\Controller\CastControllerFactory',
            'Cast\Controller\Character'     => 'Cast\Factory\Controller\CharacterControllerFactory',
            'Cast\Controller\Manager'       => 'Cast\Factory\Controller\ManagerControllerFactory',
            'Cast\Controller\Family'        => 'Cast\Factory\Controller\FamilyControllerFactory',
            'Cast\Controller\Job'           => 'Cast\Factory\Controller\JobControllerFactory',
        ),
    ),
    'lazy_services' => array(
        // mapping services to their class names is required
        // since the ServiceManager is not a declarative DIC
        'class_map' => array(
            'buzzer' => 'MyApp\Buzzer',
        ),
    ),
    'view_helpers' => array(
        'factories' => array(
            'blazon' => 'Cast\Factory\Helper\BlazonHelperFactory',
        ),
        'invokables' => array( )
    ),
    'service_manager' => array(
        'factories' => array(
            'CastService'   => 'Cast\Factory\Service\CastServiceFactory',
            'BlazonService' => 'Cast\Factory\Service\BlazonServiceFactory'
        ),
        'abstract_factories' => array(
            'Cast\Model\CharacterTable' => DefaultTableGatewayFactory::class,
            'Cast\Model\FamiliesTable' => DefaultTableGatewayFactory::class,
            'Cast\Model\JobTable' => DefaultTableGatewayFactory::class,
            'Cast\Model\BlazonTable' => DefaultTableGatewayFactory::class,
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'cast' => __DIR__ . '/../view',
        ),
    ),
     'router' => array(
         'routes' => array(

             'castmanager' => array(
                 'type'    => 'segment',
                 'may_terminate' => true,
                 'options' => array(
                     'route'    => '/castmanager',
                     'defaults' => array(
                         'controller' => 'Cast\Controller\Manager',
                         'action'     => 'index',
                     ),
                 ),
                 'child_routes' => array(
                     'families' => array(
                         'type'    => 'segment',
                         'may_terminate' => true,
                         'options' => array(
                             'route'    => '/families',
                             'defaults' => array(
                                 'controller' => 'Cast\Controller\Family',
                                 'action'     => 'index',
                             ),
                         ),
                         //children
                         'child_routes' => array(
                             'delete' => array(
                                 'type' => 'Segment',
                                 'options' => array(
                                     'route' => '/delete[/:id]',
                                     'constraints' => array(
                                         'id' => '[0-9]+'
                                     ),
                                     'defaults' => array(
                                         'action' => 'delete'
                                     )
                                 )
                             ),
                             'add' => array(
                                 'type' => 'Segment',
                                 'options' => array(
                                     'route' => '/add[/:id]',
                                     'constraints' => array(
                                         'id' => '[0-9]+'
                                     ),
                                     'defaults' => array(
                                         'action' => 'add'
                                     )
                                 )
                             ),
                             'edit' => array(
                                 'type' => 'Segment',
                                 'options' => array(
                                     'route' => '/edit[/:id]',
                                     'constraints' => array(
                                         'id' => '[0-9]+'
                                     ),
                                     'defaults' => array(
                                         'action' => 'edit'
                                     )
                                 )
                             )
                         ),
                     ),
                     'jobs' => array(
                         'type'    => 'segment',
                         'may_terminate' => true,
                         'options' => array(
                             'route'    => '/jobs',
                             'defaults' => array(
                                 'controller' => 'Cast\Controller\Job',
                                 'action'     => 'index',
                             ),
                         ),
                         //children
                         'child_routes' => array(
                             'delete' => array(
                                 'type' => 'Segment',
                                 'options' => array(
                                     'route' => '/delete[/:id]',
                                     'constraints' => array(
                                         'id' => '[0-9]+'
                                     ),
                                     'defaults' => array(
                                         'action' => 'delete'
                                     )
                                 )
                             ),
                             'add' => array(
                                 'type' => 'Segment',
                                 'options' => array(
                                     'route' => '/add[/:id]',
                                     'constraints' => array(
                                         'id' => '[0-9]+'
                                     ),
                                     'defaults' => array(
                                         'action' => 'add'
                                     )
                                 )
                             ),
                             'edit' => array(
                                 'type' => 'Segment',
                                 'options' => array(
                                     'route' => '/edit[/:id]',
                                     'constraints' => array(
                                         'id' => '[0-9]+'
                                     ),
                                     'defaults' => array(
                                         'action' => 'edit'
                                     )
                                 )
                             )
                         ),
                     ),
                     'characters' => array(
                         'type'    => 'segment',
                         'may_terminate' => true,
                         'options' => array(
                             'route'    => '/characters',
                             'defaults' => array(
                                 'controller' => 'Cast\Controller\Character',
                                 'action'     => 'index',
                             ),
                         ),
                         //children
                         'child_routes' => array(
                             'json' => array(
                                 'type' => 'Segment',
                                 'options' => array(
                                     'route' => '/json',
                                     'constraints' => array(),
                                     'defaults' => array(
                                         'action' => 'json'
                                     )
                                 )
                             ),
                             'jsonOwnerEdit' => array(
                                 'type' => 'Segment',
                                 'options' => array(
                                     'route' => '/jsonOwnerEdit',
                                     'constraints' => array(),
                                     'defaults' => array(
                                         'action' => 'jsonOwnerEdit'
                                     )
                                 )
                             ),
                             'delete' => array(
                                 'type' => 'Segment',
                                 'options' => array(
                                     'route' => '/delete[/:id]',
                                     'constraints' => array(
                                         'id' => '[0-9]+'
                                     ),
                                     'defaults' => array(
                                         'action' => 'delete'
                                     )
                                 )
                             ),
                             'add' => array(
                                 'type' => 'Segment',
                                 'options' => array(
                                     'route' => '/add[/:id]',
                                     'constraints' => array(
                                         'id' => '[0-9]+'
                                     ),
                                     'defaults' => array(
                                         'action' => 'add'
                                     )
                                 )
                             ),
                             'edit' => array(
                                 'type' => 'Segment',
                                 'options' => array(
                                     'route' => '/edit[/:id]',
                                     'constraints' => array(
                                         'id' => '[0-9]+'
                                     ),
                                     'defaults' => array(
                                         'action' => 'edit'
                                     )
                                 )
                             )
                         ),
                     ),
                     'wappen' => array(
                         'type'    => 'segment',
                         'may_terminate' => true,
                         'options' => array(
                             'route'    => '/wappen',
                             'defaults' => array(
                                 'controller' => 'Cast\Controller\Blazon',
                                 'action'     => 'index',
                             ),
                         ),
                         //children
                         'child_routes' => array(
                             'delete' => array(
                                 'type' => 'Segment',
                                 'options' => array(
                                     'route' => '/delete[/:id]',
                                     'constraints' => array(
                                         'id' => '[0-9]+'
                                     ),
                                     'defaults' => array(
                                         'action' => 'delete'
                                     )
                                 )
                             ),
                             'add' => array(
                                 'type' => 'Segment',
                                 'options' => array(
                                     'route' => '/add[/:id]',
                                     'constraints' => array(
                                         'id' => '[0-9]+'
                                     ),
                                     'defaults' => array(
                                         'action' => 'add'
                                     )
                                 )
                             ),
                             'edit' => array(
                                 'type' => 'Segment',
                                 'options' => array(
                                     'route' => '/edit[/:id]',
                                     'constraints' => array(
                                         'id' => '[0-9]+'
                                     ),
                                     'defaults' => array(
                                         'action' => 'edit'
                                     )
                                 )
                             )
                         ),
                     ),
                 ),
             ),

             'cast' => array(
                 'type'    => 'segment',
                 'may_terminate' => true,
                 'options' => array(
                     'route'    => '/cast',
                     'defaults' => array(
                         'controller' => 'Cast\Controller\Cast',
                         'action'     => 'index',
                     ),
                 ),

                 'child_routes' => array()
             ),

         ),
     ),
);