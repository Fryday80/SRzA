<?php
namespace Cms\Factory\Controller;

use Cms\Controller\ContentController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ContentControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $realServiceLocator = $serviceLocator->getServiceLocator();
        $contentService        = $realServiceLocator->get('ContentService');
        $contentInsertForm     = $realServiceLocator->get('FormElementManager')->get('Cms\Form\ContentForm');

        return new ContentController(
            $contentService,
            $contentInsertForm
            );
    }
}