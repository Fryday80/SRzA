<?php
namespace Cast;

use Cast\Helper\CharProfile;
use Cast\Model\CharacterTable;
use Cast\Model\FamiliesTable;
use Cast\Model\JobTable;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
                'Cast\Model\CharacterTable' =>  function($sm) {
                    return new CharacterTable($sm->get('Zend\Db\Adapter\Adapter'));
                },
                'Cast\Model\FamiliesTable' =>  function($sm) {
                    return new FamiliesTable($sm->get('Zend\Db\Adapter\Adapter'));
                },
                'Cast\Model\JobTable' =>  function($sm) {
                    return new JobTable($sm->get('Zend\Db\Adapter\Adapter'));
                },
            ),
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'CharProfile' => function (ServiceLocatorInterface $serviceLocator) {
                    return new CharProfile();
                }
            )
        );
    }
}