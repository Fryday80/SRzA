<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\Model\MailTemplatesTable;
use Application\Model\SystemLogTable;
use Application\Model\TempUrlTable;
use Application\Service\CacheService;
use Application\Service\MessageService;
use Application\Service\StatisticService;
use Application\View\Helper\DashboardHelper;
use Application\View\Helper\FormElementErrors;
use Application\View\Helper\FormRow;
use Application\View\Helper\InlineFromFile;
use Application\View\Helper\MyUrl;
use Application\View\Helper\sraForm;
use Zend\Mvc\MvcEvent;
use Application\View\Helper\DataTableHelper;
use Zend\Validator\AbstractValidator;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $translator = $e->getApplication()->getServiceManager()->get('translator');
        AbstractValidator::setDefaultTranslator($translator);
        $eventManager = $e->getApplication()->getEventManager();
        $statsService = $e->getApplication()->getServiceManager()->get('StatisticService');
        $eventManager->attach('dispatch', array($statsService, 'onDispatch'));
        $eventManager->attach('dispatch.error', array($statsService, 'onError'));
        $eventManager->attach('finish', array($statsService, 'onFinish'));
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
                'Application\Model\SystemLog' => function ( $serviceManager ) {
                    return new SystemLogTable( $serviceManager, $serviceManager->get('Zend\Db\Adapter\Adapter') );
                },
                'MessageService' => function($sm) {
                    $service = new MessageService();
                    return $service;
                },
                'CacheService' => function($sm) {
                    $service = new CacheService();
                    return $service;
                },
                'Application\Model\MailTemplatesTable' => function($serviceManager) {
                    return new MailTemplatesTable( $serviceManager->get('Zend\Db\Adapter\Adapter') );
                },
                'Application\Model\TempUrlTable' => function($serviceManager) {
                    return new TempUrlTable( $serviceManager->get('Zend\Db\Adapter\Adapter') );
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
                'FormRow' => function () {
                    return new FormRow();
                },
                'FormElementErrors' => function () {
                    return new FormElementErrors();
                },
                'dataTable' => function ($sm){
                    $parentLocator = $sm->getServiceLocator();
                    return new DataTableHelper($parentLocator);
                },
                'asurl' => function ($sm){
                    $accessService = $sm->getServiceLocator()->get('AccessService');
                    return new MyUrl($accessService);
                },
                'dashboardHelper'=>  function($sm){
                    return new DashboardHelper($sm);
                },
                'inlineFromFile'=>  function($sm){
                    return new InlineFromFile($sm);
                }

            )
        );
    }
}
