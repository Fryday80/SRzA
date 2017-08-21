<?php
namespace Cms\Factory\Controller;

use Cms\Controller\PageController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PageControllerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $realServiceLocator = $serviceLocator->getServiceLocator();
        $accessService      = $realServiceLocator->get('AccessService');
        $contentService        = $realServiceLocator->get('ContentService');
        return new PageController($contentService, $accessService);
    }
}