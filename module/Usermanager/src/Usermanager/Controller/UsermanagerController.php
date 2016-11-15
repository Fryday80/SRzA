<?php
namespace Usermanager\Controller;

use Zend\Http\Header\Referer;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
/*          fry anpassen
use Profile\Model\Profile;
use Profile\Form\AlbumForm;
use Profile\Form\ImageForm;
use Profile\Form\ConfirmForm;

*/
class UsermanagerController extends AbstractActionController
{
    protected $profileService;
    protected $getAuthData;
    protected $viewHelper;


    public function __construct($profileService, $getAuthData, $viewHelper)
    {
        $this->profileService = $profileService;
        $this->getAuthData = $getAuthData;
        $this->viewHelper = $viewHelper;
    }

    public function indexAction()
    {
        
        $users = $this->getAuthData->getAllUsers();
        $viewModel = new ViewModel(array(
            'viewHelper' => $this->viewHelper,
            'profiles' => $users,
            'test' => $var = (isset ($test) ? $test : 'test')) );
        return $viewModel;
    }

    public function showProfileAction ($user_id)
    {
        $user = $this->getAuthData->getUser($user_id);
        // fry andere Profil Daten
        $allowance = $this->allowEdit($user_id, $executor = 0);
        // add edit buttons to form dependent from $allowance;
        return array (
            'user' => $user[0],
            'details' => $user[1],
        );
    }

    private function allowEdit ($user_id, $executing_user)
    {
        $executing_user['user_id'] = $user_id; //testmode
        $editors = array ( 'administrator', 'editor');

        $allowance =  ($user_id == $executing_user['user_id']) ? 'self' : Null;
        $allowance =  (in_array($executing_user['role'], $editors)) ? 'editor' : $allowance;

        return $allowance;
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
        $form->setAttribute('action', '/usermanager/add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->galleryService->storeAlbum ($form->getData());
                return $this->redirect()->toRoute('usermanager');
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
            return $this->redirect()->toRoute('/usermanager');
        }
        $album = null;
        try {
            $album = $this->galleryService->getAlbumByID($id);
        } catch (\Exception $ex) {
            print ($ex);
            return $this->redirect()->toRoute('/usermanager');
        }
        $form = new AlbumForm();
        $operator = 'Edit';
        $form->get('submit')->setAttribute('value', $operator);

        $album = $this->addDate($album);    // Field 'date' needs to be added, because it is not stored in db

        $form->populateValues($album[0]);

        $form->setAttribute('action', '/usermanager/edit/' . $id);

        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->galleryService->storeAlbum ($form->getData());
                return $this->redirect()->toRoute('usermanager');
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
            return $this->redirect()->toRoute('usermanager');
        }
        if ($request->isPost()) {
            $confirmed = $request->getPost('delete_confirm', 'no');
            if ($confirmed !== 'no') {
                $this->galleryService->deleteWholeAlbum($id);
                return $this->redirect()->toRoute('usermanager');
            }
            // Redirect to list of albums
            return $this->redirect()->toRoute('usermanager');
        }
        $form = new ConfirmForm();
        $form->get('id')->setAttribute('value', $id);
        $form->setAttribute('action', '/usermanager/delete/' . $id);

        try {
            $album = $this->galleryService->getAlbumByID($id);
        } catch (\Exception $ex) {
            print ($ex);
            return $this->redirect()->toRoute('/usermanager');
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
