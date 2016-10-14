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
        //so holste dir nen service im controller
        $t = $this->getServiceLocator()->get('MediaService');
        //die funktion importiert alle files aus Upload und giebt ein array mit errors zurück ... im mom nur wenn ein file bereits existiert
        //eventuel bau ich in die import auch noch ein das thumbs erzeugt werden eventuel auch wo anders
        //wenn du willst kannst du dem MediaService auch ne private funktion machen die alle gallerys durchschaut und thumbs macht wo keine sind
        $t->import();
        //getAlbumFiles giebt die alle images zurück die halt in dem ordner sind(image is nur ein array mit path,name und so)
        $images = $t->getAlbumFiles('2016');
        print('<pre>');
        var_dump($images);
        print('</pre>');
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
