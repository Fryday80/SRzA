<?php
	namespace Application;


	use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
	use Zend\ModuleManager\Feature\ConfigProviderInterface;

	class BasicModule implements AutoloaderProviderInterface, ConfigProviderInterface
	{
		protected $dir;

		public function __construct($dir)
		{
			$this->dir = $dir;
		}

		public function getAutoloaderConfig()
		{
			if (getenv('APPLICATION_ENV') == 'production')
				return array(
					'Zend\Loader\ClassMapAutoloader' => array(
						$this->dir . '/autoload_classmap.php',
					),
				);
			return array(
				'Zend\Loader\StandardAutoloader' => array(
					'namespaces' => array(
						__NAMESPACE__ => $this->dir . '/src/' . __NAMESPACE__
					)
				)
			);
		}

		public function getConfig()
		{
			$a2 = $a3 = [];
			$a1 = include $this->dir . '/config/module.config.php';
			if (file_exists($this->dir . '/config/route.config.php'))
				$a2 = include $this->dir . '/config/route.config.php';
			if (file_exists($this->dir . '/config/configuration.config.php'))
				$a3 = include $this->dir . '/config/configuration.config.php';
			return array_merge_recursive($a1, $a2 , $a3) ;
		}
	}