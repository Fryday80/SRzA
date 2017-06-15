<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'GalleryService' => 'Gallery\Factory\GalleryServiceFactory'
        )
    ),
    'controllers' => array(
        'factories' => array(
            'Gallery\Controller\Gallery' => 'Gallery\Factory\GalleryControllerFactory'
        )
    ),
    'view_helpers' => array(
        'factories' => array(
            'randomImage' => 'Gallery\Factory\RandomImageHelperFactory',
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
                         '__NAMESPACE__' => 'Gallery\Controller',
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