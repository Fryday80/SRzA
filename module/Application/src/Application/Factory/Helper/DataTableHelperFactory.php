<?php
namespace Application\Factory\Helper;

use Zend\ServiceManager\FactoryInterface;
use Application\View\Helper\DataTableHelper;
use Zend\ServiceManager\ServiceLocatorInterface;

class DataTableHelperFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $view = $sm->getServiceLocator()->get('viewhelpermanager')->get('basePath')->getView();
        return new DataTableHelper($view);
    }
}