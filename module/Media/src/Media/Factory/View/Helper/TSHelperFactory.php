<?php
	/**
	 * Created by IntelliJ IDEA.
	 * User: Fry
	 * Date: 03.09.2017
	 * Time: 22:17
	 */

	namespace Media\Factory\View\Helper;

	use Media\View\Helper\TSHelper;
	use Zend\ServiceManager\FactoryInterface;
	use Zend\ServiceManager\ServiceLocatorInterface;

	class TSHelperFactory implements FactoryInterface
	{
		public function createService(ServiceLocatorInterface $serviceLocator)
		{
			$sm = $serviceLocator->getServiceLocator();
			$config = $sm->get('Config')['TeamSpeak']['Images'];
			$tsService = $sm->get('TSService');
			return new TSHelper($config, $tsService);
		}
	}