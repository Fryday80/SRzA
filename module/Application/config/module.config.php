<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(

    'controllers' => array(
        'invokables' => array(
            'Application\Controller\System' => 'Application\Controller\SystemController',
        )
    ),
    'router' => array(
        'routes' => array(
            'system' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/system',
                    'constraints' => array(),
                    'defaults' => array(
//                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Application\Controller\System',
                        'action'        => 'dashboard',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'dashboard' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/dashboard',
                            'constraints' => array(),
                            'defaults' => array(
                                'action' => 'dashboard',
                            )
                        )
                    ),
//                    'settings' => array(
//                        'type' => 'Segment',
//                        'options' => array(
//                            'route' => '/delete[/:id]',
//                            'constraints' => array(
//                                'id' => '[0-9]+'
//                            ),
//                            'defaults' => array(
//                                'action' => '',
//                                'id' => '[0-9]+'
//                            )
//                        )
//                    ),
                    'json' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/json',
                            'constraints' => array(),
                            'defaults' => array(
                                'action' => 'json'
                            )
                        )
                    ),
                    'formtest' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/formtest',
                            'constraints' => array(),
                            'defaults' => array(
                                'action' => 'formtest',
                            )
                        )
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'MessageService' => function($sm) {
                $service = new \Application\Service\MessageService();
                return $service;
            },
            'CacheService' => function($sm) {
                $service = new \Application\Service\CacheService();
                return $service;
            },
            'StorageService' => function($sm) {
                $service = new \Application\Service\StorageService();
                return $service;
            },
        ),
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'de_DE',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
            array(
                'type'     => 'phpArray',
                //zend defaults
                //'base_dir' => './vendor/zendframework/zendframework/resources/languages',
                //overwrite
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%.2s/Zend_Validate.php',
            ),
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/dashboard/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
