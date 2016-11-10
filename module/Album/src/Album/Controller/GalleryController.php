<?php
namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Album\Model\Album;
use Album\Form\AlbumForm;
use Google_Service_Calendar;
use Google_Client;
use Exception;

class GalleryController extends AbstractActionController
{
    protected $albumTable;

    protected function getAlbumTable()
    {
        if (!$this->albumTable) {
            $this->albumTable = $this->getServiceLocator()->get('Album\Model\AlbumTable');
        }
        return $this->albumTable;
    }

    public function indexAction()
    {
        //@todo code clean up
        /*  
        $t = $this->getServiceLocator()->get('MediaService');
        $albums = $t->getAlbumFolderNames();
        */
        $g = $this->getServiceLocator()->get('GalleryService');

        $album = $g->deleteAllAlbumImages(1);
        $viewModel = new ViewModel(array(
            'album' => $album[0],
            'images' => $album[1]
        ) );
        return $viewModel;
    }

    public function smallAction()
    {
        $t = $this->getServiceLocator()->get('MediaService');
        $images = $t->getAlbumFiles('2016');


        $g = $this->getServiceLocator()->get('GalleryTable');
        $test = $g->getImagesbyAlbumID(1);

        $viewModel = new ViewModel(array(
            'images' => $test
        ));
        $viewModel->setTemplate('Album/gallery/small.phtml');
        return $viewModel;
    }

    public function fullscreenAction()
    {
        $t = $this->getServiceLocator()->get('MediaService');
        $images = $t->getAlbumFiles('2016');
        $viewModel = new ViewModel(array(
            'images' => $images
        ));
        $viewModel->setTemplate('Album/gallery/fullscreen.phtml');
        return $viewModel;
    }


    private function split_up_timestamp ($eventAlbumID = NULL) {
        if ($eventAlbumID == NULL) {
            $timestamp = "2016-01-01 00:00:00";
        }
        else
        {
            $timestamp = $eventAlbumID->$timestamp;
        }
        list($date, $time) = explode(" ",$timestamp);
        list($year, $month, $day) = explode("-", $date);

        $splitDate = array (
                    'day'   =>  $day,
                    'month' =>  $month,
                    'year'  => $year
        );
        foreach ($splitDate as $key => $value) {
            $splitDate[$key] = intval ($value);
        }
        return $splitDate;
    }
}
