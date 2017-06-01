<?php
namespace Calendar;

use Calendar\View\Helper\UpcomingEvents;
use Calendar\View\Helper\UpcommingEvents;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Calendar\Service\CalendarService;

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
                'CalendarService' =>  function($sm) {
                    return new CalendarService($sm);
                },
            ),
        );
    }
    public function getViewHelperConfig(){
        return array(
            'factories' => array(
                'upcoming' => function ($sm) {
                    $storage = $sm->getServiceLocator()->get('Auth\Model\AuthStorage');
                    $calService = $sm->getServiceLocator()->get('CalendarService');
                    return new UpcomingEvents($storage, $calService);
                },
        ));
    }
}
