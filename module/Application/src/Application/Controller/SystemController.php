<?php
namespace Application\Controller;

use Application\Service\CacheService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Album\Model\Album;

class SystemController extends AbstractActionController
{
    public function dashboardAction()
    {
//        $cachePath = 'nav/file';
//        $data = null;
//        /** @var $cacheSerivce CacheService  */
//        $cacheSerivce = $this->serviceLocator->get('CacheService');
//        if (!$cacheSerivce->hasCache($cachePath) ) {
//            $data = 0;
//            $cacheSerivce->setCache($cachePath, $data);
//        } else {
//            $data = $cacheSerivce->getCache($cachePath);
//        }
//        return new ViewModel(array(
//            'data' => $data
//        ));
    }

    public function settingsAction()
    {

        return new ViewModel(array(
            //'table' => $albumsTable
        ));
    }
    public function jsonAction() {
        $a = json_decode($this->getRequest()->getContent());
//        $a->method;


        $result = new JsonModel(array(
            'some_parameter' => 'some value',
            'success'=>$this->getRequest()->getContent(),
        ));

        return $result;
    }
}
