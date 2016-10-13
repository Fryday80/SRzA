<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Album\Controller\Album' => 'Album\Controller\AlbumController',
            'Album\Controller\Gallery' => 'Album\Controller\GalleryController',
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
                 'options' => array(
                     'route'    => '/album[/:action][/:id]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'id'     => '[0-9]+',
                     ),
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
                                'action' => 'delete',
                                'id' => '[0-9]+'
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
                                'action' => 'add',
                                'id' => '[0-9]+'
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
                                'action' => 'edit',
                                'id' => '[0-9]+'
                            )
                        )
                    )
                ),


             ),
             'gallery' => array(
                 'type'    => 'segment',
                 'priority' => 1111,
                 'options' => array(
                     'route'    => '/gallery[/:id]',
                     'constraints' => array(
                         'id'     => '[0-9]+',
                     ),
                     'defaults' => array(
                        '__NAMESPACE__' => 'Album\Controller',
                         'controller' => 'Gallery',//eventuel ... glaub ich aber nich das des des problem is
                         'action'     => 'index',
                     ),
                 ),
                 'child_routes' => array(
                    'fullscreen' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/fullscreen[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'fullscreen',
                                'id' => '[0-9]+'
                            )
                        )
                    ),
                    'small' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/small[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'small',
                                'id' => '[0-9]+'
                            )
                        )
                    ),

                 ),
             ),
         ),
     ),
);
