<?php
namespace Application\Factory;

use Application\View\Helper\DataTableHelper;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DataTableHelperFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $parentLocator = $sm->getServiceLocator();
        $view = $parentLocator->get('viewhelpermanager')->get('basePath')->getView();
        return new DataTableHelper($view);
    }
}