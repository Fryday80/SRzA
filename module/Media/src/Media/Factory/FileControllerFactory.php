<?php
namespace Nav\Factory;


use Media\Controller\FileController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FileControllerFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return FileController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm = $serviceLocator->getServiceLocator();
        $cache = $sm->get('CacheService');
        $navTable = $sm->get('Nav\Model\NavTable');
        $roleTable = $sm->get('Auth\Model\RoleTable');
        return new FileController($cache, $navTable, $roleTable);
    }
}