<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'navigation' => 'Nav\Factory\MainNavigationFactory'
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'Nav\Controller\Nav' => 'Nav\Controller\NavController'
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'nav' => __DIR__ . '/../view'
        )
    ),
    'router' => array(
        'routes' => array(
            'nav' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/nav',
                    'defaults' => array(
                        'controller' => 'Nav\Controller\Nav',
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
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
                    ),
                    'sort' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/sort[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'sort',
                                'id' => '[0-9]+'
                            )
                        )
                    )
                )
            )
        )
    )
);
