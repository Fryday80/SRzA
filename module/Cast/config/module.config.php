<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Cast\Controller\Manager'       => 'Cast\Controller\ManagerController',
            'Cast\Controller\Character'     => 'Cast\Controller\CharacterController',
            'Cast\Controller\Family'        => 'Cast\Controller\FamilyController',
            'Cast\Controller\Job'           => 'Cast\Controller\JobController',
            'Cast\Controller\Cast'           => 'Cast\Controller\CastController',
        ),
//        'factories' => array(
//            'Cast\Controller\Cast' => function($controllerManager) {
//                $sm = $controllerManager->getServiceLocator();
//                $userTable = $sm->get('Auth\Model\UserTable');
//                $accessService = $sm->get('AccessService');
//
//                return new UsermanagerController($userTable, $accessService);
//            },
//        ),
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
