<?php
use Application\Factory\Basic\DefaultTableGatewayFactory;

return array(
    'controllers' => array(
        'invokables' => array( ),
        'factories' => array(
            'Equipment\Controller\SitePlanner' => 'Equipment\Factory\SitePlannerControllerFactory',
            'Equipment\Controller\Equipment' => 'Equipment\Factory\Controller\EquipmentControllerFactory',
        ),
    ),
    'view_helpers' => array(
        'factories' => array( ),
        'invokables' => array( )
    ),
    'service_manager' => array(
        'factories' => array(
            'TentService' => 'Equipment\Service\TentServiceFactory',
        ),
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
            'Equipment\Model\TentTable' => DefaultTableGatewayFactory::class,
            'Equipment\Model\TentTypesTable' => DefaultTableGatewayFactory::class,
            'Equipment\Model\TentColorsTable' => DefaultTableGatewayFactory::class,
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'profiles' => __DIR__ . '/../view',
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
                             'usertentall' => array(
                                 'type' => 'Segment',
                                 'options' => array(
                                     'route'    => '/:user',
                                     'defaults' => array(
                                         'controller' => 'Equipment\Controller\Equipment',
                                         'action'     => 'usertentall',
                                     ),
                                 ),
                                 'may_terminate' => true,
                                 'child_routes' => array(
                                     'usertent' => array(
                                     'type' => 'Segment',
                                     'options' => array(
                                         'route'    => '/:tentId',
                                         'defaults' => array(
                                             'controller' => 'Equipment\Controller\Equipment',
                                             'action'     => 'usertent',
                                         ),
                                     ),
                                     'may_terminate' => true,
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
                                     )
                                 )
                             )
                         ),
                     ),
                     ),
                 ),
             ),
             'site_planner' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/siteplan',
                     'defaults' => array(
                         'controller' => 'Equipment\Controller\SitePlanner',
                         'action'     => 'index',
                     ),
                 ),
                 'may_terminate' => true,
                 'child_routes' => array(
                     'eventplan' => array(
                         'type'    => 'segment',
                         'may_terminate' => true,
                         'options' => array(
                             'route'    => '/:event',
                             'defaults' => array(
                                 'controller' => 'Equipment\Controller\SitePlaner',
                                 'action'     => 'event',
                             ),
                         ),
                     )
                 ),
             ),
         ),
     ),
);