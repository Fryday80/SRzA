<?php
namespace Nav;

use Nav\Service\NavService;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Nav\Model\NavTable;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array()
            // __DIR__ . '/autoload_classmap.php',
            ,
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                )
            )
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                //'navigation' => 'Nav\Factory\MainNavigationFactory',
                'Nav\Model\NavTable' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new NavTable($dbAdapter);
                    return $table;
                },
                'NavService' => function ($sm) {
                    return new NavService($sm);
                },
            )
        );
    }
}