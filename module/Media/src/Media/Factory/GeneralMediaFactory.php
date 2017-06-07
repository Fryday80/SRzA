<?php
namespace Media\Factory;


use Exception;
use ReflectionClass;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GeneralMediaFactory implements AbstractFactoryInterface
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
        if (count($nameSpace) < 3) return false;
        if ($nameSpace[1] == 'Controller' ) {
            $requestedName .= 'Controller';
        };
        $class = new ReflectionClass($requestedName);
        $params = $class->getConstructor()->getParameters();
        return (count($params) === 1 && $params[0]->name === 'mediaService')? true: false;
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
        $serviceLocator = $serviceLocator->getServiceLocator();
        $mediaService = $serviceLocator->get('MediaService');
        $requestedName .= 'Controller';
        $new  = new $requestedName($mediaService);
        return $new;
    }
}