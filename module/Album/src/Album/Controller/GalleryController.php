<?php
namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class GalleryController extends AbstractActionController
{
    protected $galleryService;

    public function __construct($galleryService)
    {
        $this->galleryService = $galleryService;
    }

    public function indexAction()
    {
        $albums = $this->galleryService->getAllAlbums();
        $viewModel = new ViewModel(array( 'albums' => $albums ) );
        return $viewModel;
    }

    public function smallAction()
    {
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id', null);
        $album_data = $this->galleryService->getAlbum($id);
        $viewModel = new ViewModel(array(
            'album' => $album_data,
        ));
        $viewModel->setTemplate('Album/gallery/small.phtml');
        return $viewModel;
    }
}
