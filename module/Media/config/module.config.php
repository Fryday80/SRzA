<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Media\Controller\List' => 'Media\Controller\ListController',
            'Media\Controller\Upload' => 'Media\Controller\UploadController',
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
                    'route'    => '/media/',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Media\Controller',
                        'controller' => 'List',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'upload' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/upload/:title',
                            'constraints' => array(
                                'title' => '[a-zA-Z_-]+'
                            )
                        )
                    ),
                )
            ),
        )
    )
);
