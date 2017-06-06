<?php
namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class GalleryController extends AbstractActionController
{

    public function indexAction()
    {
        $galleryService = $this->getServiceLocator()->get('GalleryService');
        $albums = $galleryService->getAllAlbums();
        $viewModel = new ViewModel(array( 'albums' => $albums ) );
        return $viewModel;
    }

    public function smallAction()
    {
        $this->layout()->setVariable('showSidebar', false);
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id', null);
        $galleryService = $this->getServiceLocator()->get('GalleryService');
        $album_data = $galleryService->getAlbum($id);
        $viewModel = new ViewModel(array(
            'album' => $album_data,
        ));
        $viewModel->setTemplate('album/gallery/small.phtml');
        return $viewModel;
    }
}
