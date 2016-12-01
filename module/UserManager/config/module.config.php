<?php
use Usermanager\Controller\UsermanagerController;

return array(
    'controllers' => array(
        'invokables' => array(
            'Usermanager\Controller\Family' => 'Usermanager\Controller\FamilyController',
            'Usermanager\Controller\Job' => 'Usermanager\Controller\JobController',
        ),
        'factories' => array(
            'Usermanager\Controller\Usermanager' => function($controllerManager) {
                $sm = $controllerManager->getServiceLocator();
                $userTable = $sm->get('Auth\Model\UserTable');
                $accessService = $sm->get('AccessService');

                return new UsermanagerController($userTable, $accessService);
            },
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'profiles' => __DIR__ . '/../view',
        ),
    ),
     'router' => array(
         'routes' => array(

             'usermanager' => array(
                 'type'    => 'segment',
                 'may_terminate' => true,
                 'options' => array(
                     'route'    => '/usermanager',
                     'defaults' => array(
                         'controller' => 'Usermanager\Controller\Usermanager',
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
                    'showprofile' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/showprofile[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'showprofile'
                            )
                        )
                    ),
                    'editprofile' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/editprofile[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'editprofile'
                            )
                        )
                    )
                ),
             ),
             'families' => array(
                 'type'    => 'segment',
                 'may_terminate' => true,
                 'options' => array(
                     'route'    => '/families',
                     'defaults' => array(
                         'controller' => 'Usermanager\Controller\Family',
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
                         'controller' => 'Usermanager\Controller\Job',
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
         ),
     ),
);
