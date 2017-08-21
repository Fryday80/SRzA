<?php
namespace Application\Factory;


use Exception;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DefaultTableGatewayFactory implements AbstractFactoryInterface
{

    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $nameSpace = explode( '\\', $requestedName);
        return (isset($nameSpace[1]) && $nameSpace[1] == 'Model' )? true: false;
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return mixed
     * @throws Exception
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $adapter = $serviceLocator->get('Zend\Db\Adapter\Adapter');
        $new  = new $requestedName($adapter);
        return $new;
    }
}