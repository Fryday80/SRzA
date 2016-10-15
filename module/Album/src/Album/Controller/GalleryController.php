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
    //so jetzt hat dein controller ne helfer funktion ... und wenn man ne funktionalität haben will die in vielen views gebraucht wird machste ein viewHelper
    //des is von zend direkt so wie UserInfo ... der is glaub in auth ... den schauste dir auch mal an, und ürgendwo muss man den auch registrieren glaub ich
    //aber zum theme timestamp
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
