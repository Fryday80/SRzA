<?php
namespace Application\Factory\Helper;

use Application\Factory\Basic\MyDefaultFactory;
use Application\View\Helper\DataTableHelper;
use Zend\ServiceManager\ServiceLocatorInterface;

class DataTableHelperFactory extends MyDefaultFactory
{
    public function createService(ServiceLocatorInterface $sm) {
        parent::createService($sm);
        $view = $this->get('viewhelpermanager')->get('basePath')->getView();
        return new DataTableHelper($view);
    }
}