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
                     // /equip/tent
                     'tent' => array(
                         'type'    => 'segment',
                         'may_terminate' => true,
                         'options' => array(
                             'route'    => '/tent',
                             'defaults' => array(
                                 'controller' => 'Equipment\Controller\Equipment',
                                 'action'     => 'tent',
                             ),
                         ),
                         //children
                         'child_routes' => array(
                             // /equip/tent/add
                             'add_tent' => array(
                                 'type' => 'Segment',
                                 'options' => array(
                                     'route' => '/add[/:userId]',
                                     'constraints' => array(
                                         'userId' => '[0-9]+'
                                     ),
                                     'defaults' => array(
                                         'controller' => 'Equipment\Controller\Equipment',
                                         'action' => 'addtent'
                                     )
                                 ),
                                 'may_terminate' => true,
                             ),
                             // /equip/tent/:userId
                             'usertent_all' => array(
                                 'type' => 'Segment',
                                 'options' => array(
                                     'route'    => '/:userId',
                                     'constraints' => array(
                                         'userId' => '[0-9]+'
                                     ),
                                     'defaults' => array(
                                         'controller' => 'Equipment\Controller\Equipment',
                                         'action'     => 'usertentall',
                                     ),
                                 ),
                                 'may_terminate' => true,
                                 'child_routes' => array(
                                     // /equip/tent/:userId/show/:tentId
                                     'usertent' => array(
                                         'type' => 'Segment',
                                         'options' => array(
                                             'route'    => '/show/:tentId',
                                             'constraints' => array(
                                                 'tentId' => '[0-9]+'
                                             ),
                                             'defaults' => array(
                                                 'controller' => 'Equipment\Controller\Equipment',
                                                 'action'     => 'usertent',
                                             ),
                                         ),
                                         'may_terminate' => true,
                                     ),
                                     // /equip/tent/:userId/delete/:tentId
                                     'delete_tent' => array(
                                         'type' => 'Segment',
                                         'options' => array(
                                             'route' => '/delete/:tentId',
                                             'constraints' => array(
                                                 'tentId' => '[0-9]+'
                                             ),
                                             'defaults' => array(
                                                 'action' => 'deletetent'
                                             )
                                         ),
                                         'may_terminate' => true,
                                     ),
                                     // /equip/tent/:userId/edit/:tentId
                                     'edit_tent' => array(
                                         'type' => 'Segment',
                                         'options' => array(
                                             'route' => '/edit/:tentId',
                                             'constraints' => array(
                                                 'tentId' => '[0-9]+'
                                             ),
                                             'defaults' => array(
                                                 'action' => 'edittent'
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