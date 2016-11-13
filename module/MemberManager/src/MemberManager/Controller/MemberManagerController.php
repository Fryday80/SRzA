<?php
namespace MemberManager\Controller;

use Zend\Http\Header\Referer;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
/*          fry anpassen
use Album\Model\Album;
use Album\Form\AlbumForm;
use Album\Form\ImageForm;
use Album\Form\ConfirmForm;

*/
class ProfileController extends AbstractActionController
{
    protected $memberManagerService;

    public function __construct($memberManagerService)
    {
        $this->memberManagerService = $memberManagerService;
    }

    public function indexAction()
    {
        $users = $this->memberManagerService->getAllUser();
        $viewModel = new ViewModel(array( 'profiles' => $users ) );
        return $viewModel;
    }

    public function showProfileAction ($user_id)
    {
        $user = $this->memberManagerService->getUserByID($user_id);

        $form = new ProfileForm;
        $this->dataToForm($user, $form);
        return $form;
    }

    public function editProfileAction ($user_id, $executing_user)
    {
        $data = $this->memberManagerService->fetchWholeUserData($user_id);

    }

    public function showCashAction ($user_id)
    {

    }

    public function editCashAction ($user_id)
    {

    }

    public function showPresentationAction ()
    {

    }

    public function editPresentationAction ($user_id)
    {

    }
    private function dataToForm($data, $form){
        $new = array();
        foreach ($data as $values){
            foreach ($values as $key => $value){
                if (exists($form->get($key))){
                    $form->get($key)->setValue($value);
                }
            }
        }

    }
















    public function addAction()
    {
        $form = new AlbumForm();
        $operator = 'Neu';
        $form->get('submit')->setValue($operator);
        $form->setAttribute('action', '/album/add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->galleryService->storeAlbum ($form->getData());
                return $this->redirect()->toRoute('album');
            }
        }
        return array(
            'form' => $form
        );
    }

    public function editAction(){
        $request = $this->getRequest();
        $id = (int) $this->params()->fromRoute('id', null);
        if (! $id && !$request->isPost()) {
            return $this->redirect()->toRoute('/album');
        }
        $album = null;
        try {
            $album = $this->galleryService->getAlbumByID($id);
        } catch (\Exception $ex) {
            print ($ex);
            return $this->redirect()->toRoute('/album');
        }
        $form = new AlbumForm();
        $operator = 'Edit';
        $form->get('submit')->setAttribute('value', $operator);

        $album = $this->addDate($album);    // Field 'date' needs to be added, because it is not stored in db

        $form->populateValues($album[0]);

        $form->setAttribute('action', '/album/edit/' . $id);

        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->galleryService->storeAlbum ($form->getData());
                return $this->redirect()->toRoute('album');
            }
            dump ('not valid'); //cleanfix bugfix
        }
        return array(
            'id' => $id,
            'form' => $form
        );
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        $request = $this->getRequest();
        if (! $id && !$request->isPost()) {
            return $this->redirect()->toRoute('album');
        }
        if ($request->isPost()) {
            $confirmed = $request->getPost('delete_confirm', 'no');
            if ($confirmed !== 'no') {
                $this->galleryService->deleteWholeAlbum($id);
                return $this->redirect()->toRoute('album');
            }
            // Redirect to list of albums
            return $this->redirect()->toRoute('album');
        }
        $form = new ConfirmForm();
        $form->get('id')->setAttribute('value', $id);
        $form->setAttribute('action', '/album/delete/' . $id);

        try {
            $album = $this->galleryService->getAlbumByID($id);
        } catch (\Exception $ex) {
            print ($ex);
            return $this->redirect()->toRoute('/album');
        }
        $event = $album[0]['event'];

        return array (
            'id' => $id,
            'event' => $event,
            'form' => $form
        );
    }
    private function addDate ($data){
        $return = array();
        foreach ($data as $values){
            foreach ($values as $key => $value){
                $return[$key] = $value;
                if ($key == 'timestamp'){
                    $return['date'] = date('d.m.Y', $value);
                }
            }
        }
        return array($return);
    }
}
