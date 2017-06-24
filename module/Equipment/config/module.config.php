<?php
use Application\Factory\Basic\DefaultTableGatewayFactory;

return array(
    'controllers' => array(
        'invokables' => array( ),
        'factories' => array( ),
    ),
    'view_helpers' => array(
        'factories' => array( ),
        'invokables' => array( )
    ),
    'service_manager' => array(
        'factories' => array( ),
        'abstract_factories' => array(
//            'Cast\Model\CharacterTable' => DefaultTableGatewayFactory::class,
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'profiles' => __DIR__ . '/../view',
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