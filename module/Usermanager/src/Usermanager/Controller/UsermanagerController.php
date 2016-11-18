<?php
namespace Usermanager\Controller;

use Usermanager\Form\ShowprofileForm;
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
    private $profileService;
    private $getAuthData;
    private $viewHelper;
    private $editors_array = array();
    private $whoamI = array();
    /* @var $userTable \Auth\Model\User */
    private $userTable;


    public function __construct($userTable, $accessService, $profileService, $viewHelper)
    {
        $this->userTable = $userTable;
        // cleanfix   $this->accessService = $accessService;
        $this->profileService = $profileService;

        $this->whoamI['role'] = $accessService->getRole();
        $this->whoamI['user_id'] = $accessService->getUserID();

        $this->viewHelper = $viewHelper;
        $this->userTable = $userTable;
    }

    public function indexAction()
    {

        $allowance = $this->getAllowance($this->whoamI['user_id']);
        $operations = '<a href="#">Auswählen</a> ';
        if ($allowance == 'editor') {
            $operations .=  '<a href="#">Löschen</a>';
        }

        $users = $this->userTable->getUsers()->toArray();
        $tableData = array();
        foreach ($users as $key => $user) {
            $arr = array(
                'Name' => $user['name'],
                'email' => $user['email'],
                'Operations' => $operations,
            );
            array_push($tableData, $arr);
        }
        $viewModel = new ViewModel(array(
            'viewHelper' => $this->viewHelper,
            'profiles' => $tableData,
            'allowance' => $allowance
        ));
        return $viewModel;
    }

    public function profileAction ($user_id)
    {
        $this->editors_array = array ( 'administrator', 'editor');
        $form = new ShowprofileForm();
        $user = $this->getAuthData->getUser($user_id);
        // fry andere Profil Daten
        $allowance = $this->allowEdit($user_id, $executor = 0);
        if ($allowance == 'self') {
            $form->add('editbutton');
        }
        if (in_array($allowance, $this->editors_array)) {
            $form->add('editbutton');
            $form->add('deletebutton');
        }
        
        return array (
            'form' => $form,
            'user' => $user[0],
            'details' => $user[1],
        );
    }

    public function deleteAction ($user_id)
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $user_to_delete = $this->userTable->getUser($id);
        dumpd ($user_to_delete);
        $request = $this->getRequest();
        if (! $id && !$request->isPost()) {
            return $this->redirect()->toRoute('usermanager');
        }
        if ($request->isPost()) {
            $confirmed = $request->getPost('delete_confirm', 'no');
            if ($confirmed !== 'no') {
                $this->galleryService->deleteWholeAlbum($id); //fry delete action
                return $this->redirect()->toRoute('usermanager');
            }
            // Redirect to list of albums
            return $this->redirect()->toRoute('usermanager');
        }
        $form = new ConfirmForm();
        $form->get('realname')->setAttribute('value', $id);
        $form->setAttribute('action', '/usermanager/delete/' . $id);

        try {
            $album = $this->galleryService->getAlbumByID($id);
        } catch (\Exception $ex) {
            print ($ex);
            return $this->redirect()->toRoute('/usermanager');
        }
        $event = $album[0]['event'];

        return array (
            'viewHelper' => $this->viewHelper,
            'id' => $id,
            'event' => $event,
            'form' => $form
        );
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
    private function getAllowance ($id = 0)
    {
        if ($id == 0)
        {
            if ($this->whoamI['role'] == 'Administrator' || $this->whoamI['role'] == 'Profiladmin') //salt n fry Roles zuteilen
            {
                return 'editor';
            }
            return;
        }
        if ($id !== 0){
            if ($this->whoamI['role'] == 'Administrator' || $this->whoamI['role'] == 'Profiladmin') //salt n fry Roles zuteilen
            {
                return 'editor';
            }
            if ($id == $this->whoamI['user_id']){
                return 'self';
            }
        }
    }
}
