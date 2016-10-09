<?php
namespace Cms\Factory;

use Cms\Controller\ContentController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ContentControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $realServiceLocator = $serviceLocator->getServiceLocator();
        $postService        = $realServiceLocator->get('Cms\Service\PostServiceInterface');
        $postInsertForm     = $realServiceLocator->get('FormElementManager')->get('Cms\Form\PostForm');

        return new ContentController(
            $postService,
            $postInsertForm
            );
    }
}