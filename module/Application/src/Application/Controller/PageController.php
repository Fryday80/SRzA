<?php
namespace Application\Controller;

use Application\Service\CacheService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Album\Model\Album;

class PageController extends AbstractActionController
{
    public function dashboardAction()
    {
        $cachePath = 'nav/file';
        $data = null;
        /** @var $cacheSerivce CacheService  */
        $cacheSerivce = $this->serviceLocator->get('CacheService');
        if (!$cacheSerivce->hasCache($cachePath) ) {
            $data = 0;
            $cacheSerivce->setCache($cachePath, $data);
        } else {
            $data = $cacheSerivce->getCache($cachePath);
        }
        return new ViewModel(array(
            'data' => $data
        ));
    }

    public function settingsAction()
    {

        return new ViewModel(array(
            //'table' => $albumsTable
        ));
    }
}
