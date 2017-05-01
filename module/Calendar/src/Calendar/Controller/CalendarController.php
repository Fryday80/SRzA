<?php
namespace Calendar\Controller;

use Calendar\DataTable\CalendarTable;
use Calendar\Form\CalendarSelectionForm;
use Calendar\Service\CalendarService;
use Zend\Http\Header\Referer;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class CalendarController extends AbstractActionController
{
    private $sm;
    /** @var CalendarService $calendar*/
    private $calendar;

    public function __construct($sm)
    {
        $this->sm = $sm;
        $this->calendar = $sm->get('CalendarService');
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        $form = new CalendarSelectionForm();
        $form->get('submit')->setAttribute('value', 'index');

        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->galleryService->storeAlbum ($form->getData());
                return $this->redirect()->toRoute('calendar');
                $requested = true;
            }
        }
        $form->populateValues($form);

        if ($requested){
            // get calendar data from/to
        } else {
            // get calendar data from now on == all
            $appointments = $this->calendar->getAllAppointments();
            // get calendar data from now on == all
        }

        $viewModel = new ViewModel(array( 'form' => $form ) );
        return $viewModel;
    }

    public function addAction()
    {
//        $form = new AlbumForm();
//        $operator = 'Neu';
//        $form->get('submit')->setValue($operator);
//        $form->setAttribute('action', '/album/add');
//
//        $request = $this->getRequest();
//        if ($request->isPost()) {
//            $form->setData($request->getPost());
//            if ($form->isValid()) {
//                $this->galleryService->storeAlbum ($form->getData());
//                return $this->redirect()->toRoute('album');
//            }
//        }
//        return array(
//            'form' => $form
//        );
    }

    public function editAction(){
//        $request = $this->getRequest();
//        $id = (int) $this->params()->fromRoute('id', null);
//        if (! $id && !$request->isPost()) {
//            return $this->redirect()->toRoute('/album');
//        }
//        $album = null;
//        try {
//            $album = $this->galleryService->getAlbumByID($id);
//        } catch (\Exception $ex) { 
//            print ($ex);
//            return $this->redirect()->toRoute('/album');
//        }        
//        $form = new AlbumForm();
//        $operator = 'Edit';
//        $form->get('submit')->setAttribute('value', $operator);
//        
//        $album = $this->addDate($album);    // Field 'date' needs to be added, because it is not stored in db
//        
//        $form->populateValues($album[0]);
//
//        $form->setAttribute('action', '/album/edit/' . $id);
//
//        if ($request->isPost()) {
//            $form->setData($request->getPost());
//            if ($form->isValid()) {
//                $this->galleryService->storeAlbum ($form->getData());
//                return $this->redirect()->toRoute('album');
//            }
//        }
//        return array(
//            'id' => $id,
//            'form' => $form
//        );
    }

    public function deleteAction() {
//        $id = (int) $this->params()->fromRoute('id', 0);
//        $request = $this->getRequest();
//        if (! $id && !$request->isPost()) {
//           return $this->redirect()->toRoute('album');
//        }
//        if ($request->isPost()) {
//            $confirmed = $request->getPost('delete_confirm', 'no');
//            if ($confirmed !== 'no') {
//                $this->galleryService->deleteWholeAlbum($id);
//                return $this->redirect()->toRoute('album');
//            }
//            // Redirect to list of albums
//            return $this->redirect()->toRoute('album');
//        }
//        $form = new ConfirmForm();
//        $form->get('id')->setAttribute('value', $id);
//        $form->setAttribute('action', '/album/delete/' . $id);
//
//        try {
//            $album = $this->galleryService->getAlbumByID($id);
//        } catch (\Exception $ex) {
//            print ($ex);
//            return $this->redirect()->toRoute('/album');
//        }
//        $event = $album[0]['event'];
//
//        return array (
//            'id' => $id,
//            'event' => $event,
//            'form' => $form
//        );
    }
}
