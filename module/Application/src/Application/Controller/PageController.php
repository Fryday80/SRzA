<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Album\Model\Album;

class PageController extends AbstractActionController
{
    public function dashboardAction()
    {

        return new ViewModel(array(
            //'table' => $albumsTable
        ));
    }

    public function settingsAction()
    {

        return new ViewModel(array(
            //'table' => $albumsTable
        ));
    }
}
