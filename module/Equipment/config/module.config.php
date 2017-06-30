<?php
use Application\Factory\Basic\DefaultTableGatewayFactory;

return array(
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
            'Equipment\Model\SitePlannerTable' => DefaultTableGatewayFactory::class,
            'Equipment\Model\EquipTable' => DefaultTableGatewayFactory::class,
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'equip' => __DIR__ . '/../view',
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