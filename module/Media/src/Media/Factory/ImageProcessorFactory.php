<?php
namespace Media\Factory;

use Media\Service\ImageProcessor;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ImageProcessorFactory implements FactoryInterface
{
	private $config = array();
	private $classConfig = array();

    public function createService(ServiceLocatorInterface $sm) {
        $this->config = $sm->get('Config');
        $this->getConfigData();
        return new ImageProcessor($this->classConfig);
    }

	private function getConfigData()
	{
		// get keys that contain ImageProcessor (ModuleName_ImageProcessor)
		array_filter(
			$this->config,
			function ($k) {
				if ($this->isImageProcessorKey($k))
					$this->classConfig[$k] = $this->config[$k];
			},
			ARRAY_FILTER_USE_KEY
		);
    }

    private function isImageProcessorKey($key)
	{
		$key = strtolower($key);
		return (strpos($key, 'imageprocessor') !== false) ? true : false;
	}
}