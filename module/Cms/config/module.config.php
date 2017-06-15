<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'Cms\Service\PostServiceInterface' => 'Cms\Factory\PostServiceFactory',
            'Cms\Mapper\PostMapperInterface' => 'Cms\Factory\ZendDbSqlMapperFactory'
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view'
        )
    ),
    'controllers' => array(
        'factories' => array(
            'Cms\Controller\Content' => 'Cms\Factory\ContentControllerFactory',
            'Cms\Controller\Page' => 'Cms\Factory\PageControllerFactory',
        )
    ),
    'router' => array(
        'routes' => array(
            'page' => array(
                'type' => 'literal',
                'priority' => -1000,
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Cms\Controller',
                        'controller' => 'Page',
                        'action'     => 'index',
                        'title'      => '_default'
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'title' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => ':title',
                            'constraints' => array(
                                'title' => '[a-zA-Z_-]+'
                            )
                        )
                    ),
                )
            ),
            'home' => array(
                'type' => 'literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Cms\Controller',
                        'controller' => 'Page',
                        'action'     => 'index',
                        'title'      => '_default'
                    ),
                )
            ),
            'cms' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/cms',
                    'defaults' => array(
                        'controller' => 'Cms\Controller\Content',
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'detail' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/:id',
                            'defaults' => array(
                                'action' => 'detail'
                            ),
                            'constraints' => array(
                                'id' => '[1-9]\d*'
                            )
                        )
                    ),
                    'add' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/add',
                            'defaults' => array(
                                'action' => 'add'
                            )
                        )
                    ),
                    'edit' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/edit/:id',
                            'defaults' => array(
                                'action' => 'edit'
                            ),
                            'constraints' => array(
                                'id' => '\d+'
                            )
                        )
                    ),
                    'delete' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/delete/:id',
                            'defaults' => array(
                                'action' => 'delete'
                            ),
                            'constraints' => array(
                                'id' => '\d+'
                            )
                        )
                    )
                )
            )
        )
    )
);