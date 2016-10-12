<?php
namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Album\Model\Album;
use Album\Form\AlbumForm;

class GalleryController extends AbstractActionController
{

    protected $albumTable;

    public function indexAction()
    {
        $viewModel = new ViewModel(array(
        ));
        //mach 2 actions eine für small eine für fullscreen k k aber da die viel gleichen code haben in ne extra funktion auslagern
        //und nohc ein action für die alben auswahl ... besten mit nem vorschaubild
        if (true) {
            $viewModel->setTemplate('Album/gallery/small.phtml');
        } else {
            $viewModel->setTerminal(true);
        }
        return $viewModel;
    }

    public function smallAction()
    {
        $viewModel = new ViewModel(array());
        if (true) {
            $viewModel->setTemplate('Album/gallery/small.phtml');
        } else {
            $viewModel->setTerminal(true);
        }
        return $viewModel;
    }

    public function fullscreenAction()
    {
        $viewModel = new ViewModel(array());
        if (true) {
            $viewModel->setTemplate('Album/gallery/small.phtml');
        } else {
            $viewModel->setTerminal(true);
        }
        return $viewModel;
    }
}
