<?php
namespace Gallery\Controller;

use Gallery\Service\GalleryService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class GalleryController extends AbstractActionController
{
    private $galleryService;

    public function __construct(GalleryService $galleryService)
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
        $this->layout()->setVariable('showSidebar', false);
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id', null);
        $album_data = $this->galleryService->getAlbum($id);
        $viewModel = new ViewModel(array(
            'album' => $album_data,
        ));
        $viewModel->setTemplate('gallery/gallery/small.phtml');
        return $viewModel;
    }
}
