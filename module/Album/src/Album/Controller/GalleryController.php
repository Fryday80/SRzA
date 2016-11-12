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
       // $this->galleryService = $this->getServiceLocator()->get('GalleryService');
        $albums = $this->galleryService->getAllAlbums();
        $viewModel = new ViewModel(array( 'albums' => $albums ) );
        return $viewModel;
    }

    public function smallAction()
    {
        $id = (isset($id))?: 1;
        $album_data = $this->galleryService->fetchWholeAlbumData($id);

        $viewModel = new ViewModel(array(
            'album' => $album_data[0],
            'images' => $album_data[1]
        ));
        $viewModel->setTemplate('Album/gallery/small.phtml');
        return $viewModel;
    }
}
