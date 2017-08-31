<?php
namespace Media\Factory;

use Auth\Service\AccessService;
use Media\Service\ImageProcessor;
use Media\Service\MediaService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MediaServiceFactory implements FactoryInterface
{
	private $config;
	private $classConfig = array();

    public function createService(ServiceLocatorInterface $sm) {
    	/** @var AccessService $accessService */
        $accessService = $sm->get('AccessService');
        /** @var ImageProcessor $imageProcessor */
        $imageProcessor = $sm->get('ImageProcessor');
		$this->config = $sm->get('Config');
		$this->getConfigData();
        return new MediaService($this->classConfig, $accessService, $imageProcessor);
    }

	private function getConfigData()
	{
		array_filter(
			$this->config,
			function ($k) {
				if ($this->isClassKey($k, 'mediaservice'))
					$this->classConfig[$k] = $this->config[$k];
			},
			ARRAY_FILTER_USE_KEY
		);
	}

	private function isClassKey($key, $class)
	{
		$key = strtolower($key);
		return (strpos($key, $class) !== false) ? true : false;
	}
}