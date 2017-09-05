<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

use Application\Factory\DefaultTableGatewayFactory;

return array(

    'controllers' => array(
        'factories' => array(
            'Application\Controller\System' => 'Application\Factory\Controller\SystemControllerFactory',
        )
    ),
	'controller_plugins' => array(
		'invokables' => array(
			'defaultView' => 'Application\Controller\Plugin\DefaultView',
		),
	),
    'service_manager' => array(
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
        'invokables' => array(
            'CacheService' => 'Application\Service\CacheService',
            'Application\Model\SystemLog' => 'Application\Model\SystemLog',
        ),
        'factories' => array(
            'SystemService'    => 'Application\Factory\Service\SystemServiceFactory',
            'StatisticService' => 'Application\Factory\Service\StatisticServiceFactory',
            'MessageService'   => 'Application\Factory\Service\MessageServiceFactory',
        ),
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
            'Application\Model\SystemLogTable' => DefaultTableGatewayFactory::class,
            'Application\Model\DynamicHashTable' => DefaultTableGatewayFactory::class,
            'Application\Model\MailTemplatesTable' => DefaultTableGatewayFactory::class,
        )
    ),
    'form_elements' => array(
        'invokables' => array(
            'FormTextSearch' => 'Application\Form\Element\TextSearch',
            'textsearch' => 'Application\Form\Element\TextSearch',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'FormRow' => 'Application\View\Helper\FormRow',
            'FormElementErrors' => 'Application\View\Helper\FormElementErrors',
            'InlineFromFile' => 'Application\View\Helper\InlineFromFile',
            'convert' => 'Application\View\Helper\ConverterHelper',
            'formtextsearch' => 'Application\View\Helper\FormTextSearch',
            'formelement' => 'Application\View\Helper\FormElement',
        ),
        'factories' => array(
            'asurl' => 'Application\Factory\Helper\MyUrlFactory',
            'dataTable' => 'Application\Factory\Helper\DataTableHelperFactory',
        )
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
            'error/404'               => __DIR__ . '/../view/application/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/application/error/index.phtml',
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
    'router' => array(
        'routes' => array(
			'test' => array(
				'type'    => 'segment',
				'options' => array(
					'route'    => '/test',
					'constraints' => array(),
					'defaults' => array(
						'controller'    => 'Application\Controller\System',
						'action'        => 'test',
					),
				),
				'may_terminate' => true,
			),
			'phpinfo' => array(
				'type'    => 'segment',
				'options' => array(
					'route'    => '/php',
					'constraints' => array(),
					'defaults' => array(
						'controller'    => 'Application\Controller\System',
						'action'        => 'php',
					),
				),
				'may_terminate' => true,
			),
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
                            'route' => '/dashboard[/]',
                            'constraints' => array(),
                            'defaults' => array(
                                'action' => 'dashboard',
                            )
                        ),
                        'may_terminate' => true,
                    ),
                    'mailTemplates' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/mailTemplates',
                            'constraints' => array(),
                            'defaults' => array(
                                'action' => 'mailTemplatesIndex',
                            )
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'add' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/add',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'action' => 'addMailTemplate',
                                    )
                                ),
                                'may_terminate' => true,
                            ),
                            'template' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:templateName',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'action' => 'mailTemplate',
                                    )
                                ),
                                'may_terminate' => true,
                            ),
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
            'message' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/message',
                    'constraints' => array(),
                    'defaults' => array(
//                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Application\Controller\System',
                        'action'        => 'message',
                    ),
                ),
                'may_terminate' => true,
            )
        ),
    ),
);
