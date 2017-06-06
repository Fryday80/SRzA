<?php
use Album\Controller\GalleryController;
use Album\Service\GalleryService;
use Album\Controller\AlbumController;
return array(
    'service_manager' => array(
        'invokables' => array(
            'GalleryService' => 'Album\Service\GalleryService'
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'Album\Controller\Gallery' => 'Album\Controller\GalleryController'
        ),
    ),
    'view_helpers' => array(
        'factories' => array(
            'randomImage' => 'Album\Factory\RandomImageHelperFactory',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'album' => __DIR__ . '/../view',
        ),
    ),
    'router' => array(
         'routes' => array(
             'gallery' => array(
                 'type'    => 'segment',
                 'may_terminate' => true,
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
                              //  'id' => '[*]+'
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
