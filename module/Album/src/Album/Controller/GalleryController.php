<?php
namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Album\Model\Album;
use Album\Form\AlbumForm;

class GalleryController extends AbstractActionController
{

    protected $albumTable;

//    private function findedenfehler ()
//    {
//        if (property_exists ($this, 'getAlbumTable()') )
//        {
//            $this->getAlbumTable()->fetchAll();
//        }
//        else
//        {
//            echo 'ich mog ned';
//        }
//    }

    public function indexAction()
    {
        $viewModel = new ViewModel(array(
//            'test' => $this->findedenfehler(),
//            'albums' => $this->getAlbumTable()->fetchAll()
        ) );
        return $viewModel;
    }

    public function smallAction()
    {
        $viewModel = new ViewModel(array());
            $viewModel->setTemplate('Album/gallery/small.phtml');
        return $viewModel;
    }

    public function fullscreenAction()
    {
        $viewModel = new ViewModel(array());
        $viewModel->setTerminal(true);
        return $viewModel;
    }




//    public function fullscreenAction()
//    {
//        $viewModel = new ViewModel(array());
//        if (true) {
//            $viewModel->setTemplate('Album/gallery/small.phtml');
//        } else {
//            $viewModel->setTerminal(true);
//        }
//        return $viewModel;
//    }
}
