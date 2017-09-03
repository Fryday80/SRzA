<?php
namespace Application\Controller\Plugin;

use Media\Service\ImageProcessor;
use Media\Service\MediaException;
use Media\Service\MediaService;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Class ImageUpload <br/>
 * Controller Plugin to manage Image Uploads
 *
 * @package Application\Controller\Plugin
 */
class MediaPlugin extends AbstractPlugin
{
	protected $config;
	/** @var MediaService  */
	public $mediaService;
	/** @var ImageProcessor  */
	public $imageProcessor;
	/** @var ImageUpload */
	public $imageUpload;

	function __construct(Array $config, MediaService $mediaService)
	{
		$this->config = $config;
		$this->mediaService = &$mediaService;
		$this->imageProcessor = &$mediaService->imageProcessor;
		$this->imageUpload = new ImageUpload($config, $this->mediaService);
	}
}