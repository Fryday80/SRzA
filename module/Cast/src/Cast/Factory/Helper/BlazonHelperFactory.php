<?php
namespace Cast\Factory\Helper;

use Album\Controller\AlbumController;
use Cast\View\Helper\BlazonHelper;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BlazonHelperFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        return new BlazonHelper($sm->getServiceLocator()->get('BlazonService'));
    }
}