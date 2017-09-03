<?php
	/**
	 * Created by IntelliJ IDEA.
	 * User: Fry
	 * Date: 03.09.2017
	 * Time: 22:17
	 */

	namespace Media\Factory\View\Helper;


	use Media\View\Helper\MediaHelper;
	use Zend\ServiceManager\FactoryInterface;
	use Zend\ServiceManager\ServiceLocatorInterface;

	class MediaHelperFactory implements FactoryInterface
	{
		public function createService(ServiceLocatorInterface $serviceLocator)
		{
			$sm = $serviceLocator->getServiceLocator();
			$mediaService = $sm->get('MediaService');
			return new MediaHelper($mediaService);
		}
	}