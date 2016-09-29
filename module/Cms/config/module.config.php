<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
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
            'Cms\Controller\List' => 'Cms\Factory\ListControllerFactory',
            'Cms\Controller\Write' => 'Cms\Factory\WriteControllerFactory',
            'Cms\Controller\Delete' => 'Cms\Factory\DeleteControllerFactory'
        )
    ),
    'router' => array(
        'routes' => array(
            'cms' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/cms',
                    'defaults' => array(
                        'controller' => 'Cms\Controller\List',
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
                                'controller' => 'Cms\Controller\Write',
                                'action' => 'add'
                            )
                        )
                    ),
                    'edit' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/edit/:id',
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Write',
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
                                'controller' => 'Cms\Controller\Delete',
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