<?php
use MemberManager\Controller\MemberManagerController; ///fry anpassen

return array(
    'controllers' => array(
        'invokables' => array(
           // 'MemberManager\Controller\MemberManager' => 'MemberManager\Controller\AlbumController',
           // 'MemberManager\Controller\Gallery' => 'MemberManager\Controller\GalleryController',
        ),
        'factories' => array(
            'MemberManager' => function($controllerManager) {
                $sm = $controllerManager->getServiceLocator();
                $galleryService = $sm->get('MemberManagerService');
                return new MemberManagerController($galleryService);
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

             'profiles' => array(
                 'type'    => 'segment',
                 'may_terminate' => true,
                 'options' => array(
                     'route'    => '/profiles',
                     'defaults' => array(
                         'controller' => 'MemberManager\Controller\MemberManager',
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
