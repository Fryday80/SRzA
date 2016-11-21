<?php
namespace Usermanager;

use Usermanager\Model\FamiliesTable;
use Usermanager\Model\JobTable;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
//                 __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
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
                'Usermanager\Model\FamiliesTable' =>  function($sm) {
                    return new FamiliesTable($sm->get('Zend\Db\Adapter\Adapter'));
                },
                'Usermanager\Model\JobTable' =>  function($sm) {
                    return new JobTable($sm->get('Zend\Db\Adapter\Adapter'));
                },
            ),
        );
    }
}