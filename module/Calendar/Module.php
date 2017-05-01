<?php
namespace Calendar;

use Calendar\DataTable\CalendarTable;
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
                'Calendar\DataTable\Calendar' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new CalendarTable($dbAdapter);
                },
            ),
        );
    }
    public function getViewHelperConfig()
    {
        return array(
//            'factories' => array(
//                'randomImage' => function ($sm){
//                    $galleryService = $sm->getServiceLocator()->get('GalleryService');
//                    return new RandomImageHelper($galleryService);
//                }
//            )
        );
    }
}