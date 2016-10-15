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
                    'fullscreen' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/fullscreen[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'fullscreen',
                                'id' => '1'
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
                                'id' => '1'
                            )
                        )
                    ),

                 ),
             ),
         ),
     ),
);
