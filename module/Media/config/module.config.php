<?php
use Application\Factory\DefaultTableGatewayFactory;
use Media\Factory\GeneralMediaFactory;

return array(
	'MediaService' => array(
		'thumbs' => array (
			'relPath' => '/_thumbs',
			'sX' => 100,
			'sY' => 100,
			'bX' => 250,
			'bY' => 250,
		),
		'profile_images' => array (
			'relPath' => '/users',
			'maxSide' => 1000,
		),
	),
	'ImageProcessor' => array (
		'root' 	   => getcwd(),
		'dataRoot' => getcwd() . '/Data',
		'default_page_size' => array(
			'x' => 500,
			'y' => 500
		),
	),
    'controllers' => array(
        'abstract_factories' => array(
            'Media\Controller\FileBrowser'  => GeneralMediaFactory::class,
            'Media\Controller\List'         => GeneralMediaFactory::class,
            'Media\Controller\File'         => GeneralMediaFactory::class,
        )
    ),
	'controller_plugins' => array(
		'factories' => array(
			'image' => 'Media\Factory\ImagePluginFactory',
		),
	),
    'service_manager' => array(
        'factories' => array(
            'MediaService' => 'Media\Factory\MediaServiceFactory',
			'ImageProcessor' => 'Media\Factory\ImageProcessorFactory',
        ),
        'abstract_factories' => array(
            'Media\Model\FileTable' => DefaultTableGatewayFactory::class
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'media' => __DIR__ . '/../view'
        )
    ),
	'view_helpers' => array(
		'invokables' => array( ),
		'factories' => array(
			'media' => 'Media\Factory\View\Helper\MediaHelperFactory'
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
                                'type'     => 'regex',
                                'options'   => array(
                                    'regex' => '/action(?<path>.*)',
                                    'defaults'  => array(
                                        'controller'    => 'FileBrowser',
                                        'action'        => 'action',
                                    ),
                                    'spec'  => '/path%path%'
                                ),
                            ),
                        )
                    ),
                    'filebrowserembedded'  => array(
                        'type'  => 'literal',
                        'options'   => array(
                            'route'     => '/efilebrowser',
                            'defaults'  => array(
                                'controller'    => 'FileBrowser',
                                'action'        => 'embedded',
                            ),
                        ),
                    ),
                    'file'  => array(
                        'type'  => 'regex',
                        'options'   => array(
                            'regex'     => '/file(?<path>\/.*)',
                            'defaults'  => array(
                                'controller'    => 'file',
                                'action'        => 'file',
                            ),
                            'spec'  => '/path%path%'
                        ),
                    ),
//                    'image' => array(
//                        'type' => 'regex',
//                        'options' => array(
//                            'regex' => '/image(?<path>\/.*)',
//                            'defaults' => array(
//                                'controller' => 'file',
//                                'action' => 'image',
//                            ),
//                            'spec' => '/path%path%'
//                        ),
//                    ),

                ),//child_routes
            ),
        )
    )
);