<?php
use Album\Controller\GalleryController;
use Album\Service\GalleryService;
use Album\Controller\AlbumController;
return array(
    'controllers' => array(
        'invokables' => array(
           // 'Album\Controller\Album' => 'Album\Controller\AlbumController',
           // 'Album\Controller\Gallery' => 'Album\Controller\GalleryController',
        ),
        'factories' => array(
            'Album\Controller\Gallery' => function($controllerManager) {
                $sm = $controllerManager->getServiceLocator();
                $galleryService = $sm->get('GalleryService');
                return new GalleryController($galleryService);
            },
            'Album\Controller\Album' =>  function($controllerManager) {
                $sm = $controllerManager->getServiceLocator();
                $galleryService = $sm->get('GalleryService');
                return new AlbumController($galleryService);
            },
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'album' => __DIR__ . '/../view',
        ),
    ),
     'router' => array(
         'routes' => array(

             'album' => array(
                 'type'    => 'segment',
                 'may_terminate' => true,
                 'options' => array(
                     'route'    => '/album',
                     'defaults' => array(
                         'controller' => 'Album\Controller\Album',
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
             'gallery' => array(
                 'type'    => 'segment',
                 'may_terminate' => true,//des brauchst wenn man die gallery auch ohne eine child route benutzen will also /gallery  und nich /gallery/fullscreen oder so
                 'options' => array(
                     'route'    => '/gallery',
                     'defaults' => array(
                        '__NAMESPACE__' => 'Album\Controller',
                         'controller' => 'Gallery',
                         'action'     => 'index',
                     ),
                 ),
                 'child_routes' => array(
                    'small' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/small[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'small',
                                'id' => '1'
                            )
                        )
                    ),

                 ),
             ),
         ),
     ),
);
