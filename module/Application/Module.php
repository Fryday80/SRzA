<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\Model\ActiveUsers;
use Application\Model\PageHits;
use Application\Model\SystemLog;
use Application\Service\StatisticService;
use Application\View\Helper\MyUrl;
use Application\View\Helper\sraForm;
use Zend\Mvc\MvcEvent;
use Application\View\Helper\DataTableHelper;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $statsService = $e->getApplication()->getServiceManager()->get('StatisticService');
        $eventManager->attach('dispatch', array($statsService, 'onDispatch'));
        date_default_timezone_set ("Europe/Berlin");
        return $this;
    }
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'StatisticService' => function ( $sm ) {
                    return new StatisticService( $sm );
                },
                'Application\Model\ActiveUsers' => function ( $serviceManager ) {
                    return new ActiveUsers( $serviceManager->get('Zend\Db\Adapter\Adapter') );
                },
                'Application\Model\PageHits' => function ( $serviceManager ) {
                    return new PageHits( $serviceManager->get('Zend\Db\Adapter\Adapter') );
                },
                'Application\Model\SystemLog' => function ( $serviceManager ) {
                    return new SystemLog( $serviceManager->get('Zend\Db\Adapter\Adapter') );
                },
            )
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'form' => function () {
                    return new sraForm();
                },
                'dataTable' => function ($sm){
                    $parentLocator = $sm->getServiceLocator();
                    return new DataTableHelper($parentLocator);
                },
                'asurl' => function ($sm){
                    $accessService = $sm->getServiceLocator()->get('AccessService');
                    return new MyUrl($accessService);
                },

            )
        );
    }
}
