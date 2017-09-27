<?php
use Application\Factory\DefaultTableGatewayFactory;
use Media\Factory\GeneralMediaFactory;

return array(
    'controllers' => array(
    	'factories' => array(
			'Media\Controller\TeamSpeak'    => 'Media\Factory\Controller\TeamSpeakControllerFactory'
		),
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
            'MediaService'   => 'Media\Factory\MediaServiceFactory',
			'ImageProcessor' => 'Media\Factory\ImageProcessorFactory',
			'TSService' 	 => 'Media\Factory\Service\TeamSpeakServiceFactory'
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
);