<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Media\Controller\List'         => 'Media\Controller\ListController',
            'Media\Controller\Upload'       => 'Media\Controller\UploadController',
            'Media\Controller\File'         => 'Media\Controller\FileController',
            'Media\Controller\FileBrowser'  => 'Media\Controller\FileBrowserController',
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'media' => __DIR__ . '/../view'
        )
    ),
    'router' => array(
        'routes' => array(
            'media' => array(
                'type' => 'literal',
                'options' => array(
                    'route'     => '/media',
                    'defaults'  => array(
                        '__NAMESPACE__' => 'Media\Controller',
                        'controller'    => 'List',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes'  => array(
                    'filebrowser'  => array(
                        'type'  => 'literal',
                        'options'   => array(
                            'route'     => '/filebrowser',
                            'defaults'  => array(
                                'controller'    => 'FileBrowser',
                                'action'        => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes'  => array(
                            'action'     => array(
                                'type'     => 'Literal',
                                'options'   => array(
                                    'route' => '/action',
                                    'defaults'  => array(
                                        'controller'    => 'FileBrowser',
                                        'action'        => 'action',
                                    ),
                                ),
                            ),
                        )
                    ),
                    'download'  => array(
                        'type'  => 'regex',
                        'options'   => array(
                            'regex'     => '/download(?<path>\/.*)',
                            'defaults'  => array(
                                'controller'    => 'file',
                                'action'        => 'download',
                            ),
                            'spec'  => '/path%path%'
                        ),
                    ),
                    'image' => array(
                        'type' => 'regex',
                        'options' => array(
                            'regex' => '/image(?<path>\/.*)',
                            'defaults' => array(
                                'controller' => 'file',
                                'action' => 'image',
                            ),
                            'spec' => '/path%path%'
                        ),
                    ),
                    'upload' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/upload/:title',
                            'constraints' => array(
                                'title' => '[a-zA-Z_-]+'
                            )
                        )
                    ),

                ),//child_routes
            ),
        )
    )
);
