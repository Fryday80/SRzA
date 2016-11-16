<?php
use Usermanager\Controller\UsermanagerController;

return array(
    'controllers' => array(
        'invokables' => array( ),
        'factories' => array(
            'Usermanager\Controller\Usermanager' => function($controllerManager) {
                $sm = $controllerManager->getServiceLocator();
                $profileService = $sm->get('ProfileService');
                $getAuthData = $sm->get('GetauthService');
                $viewHelper = $sm->get('ViewHelper');

                return new UsermanagerController($profileService, $getAuthData, $viewHelper);
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
