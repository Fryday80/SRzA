<?php
use Usermanager\Controller\UsermanagerController;

return array(
    'controllers' => array(
        'invokables' => array( ),
        'factories' => array(
            'Usermanager\Controller\Usermanager' => function($controllerManager) {
                $sm = $controllerManager->getServiceLocator();
                $userTable = $sm->get('Auth\Model\UserTable');
                $accessService = $sm->get('AccessService');
                $profileService = $sm->get('ProfileService');
                $datatableHelper = $sm->get('DatatableHelper');

                return new UsermanagerController($userTable, $accessService, $profileService, $datatableHelper);
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
                    'profile' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/profile[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'profile'
                            )
                        )
                    )
                ),
             ),
         ),
     ),
);
