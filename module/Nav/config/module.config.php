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
                )
            )
        )
    )
);