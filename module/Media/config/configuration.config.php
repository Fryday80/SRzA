<?php

	return array(
		'TeamSpeak' => array(
			'ip' => '4istfast1.de',

		),

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
	);