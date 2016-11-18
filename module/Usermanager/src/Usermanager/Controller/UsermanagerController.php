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
    private $controller = 'usermanager';

    private $editors_array = array ( 'administrator', 'editor');

    private $whoamI = array();
    /* @var $userTable \Auth\Model\User */
    private $userTable;

    private $profileService;


    private $datatableHelper;


    public function __construct($userTable, $accessService, $profileService, $datatableHelper)
    {
        $this->userTable = $userTable;

        $this->whoamI['role'] = $accessService->getRole();
        $this->whoamI['user_id'] = $accessService->getUserID();

        $this->profileService = $profileService;

        $this->datatableHelper = $datatableHelper;
    }

    public function indexAction()
    {

        $allowance = $this->getAllowance($this->whoamI['user_id']);
        $operations = array ('profile' => 'Auswählen');
        if ($allowance == 'editor') {
            $operations['delete'] =  'Löschen';
        }

        $users = $this->userTable->getUsers()->toArray();
        $tableData = array();
        $hidden_columns = array ('id');
        foreach ($users as $key => $user) {
            $arr = array(
                'id'    => $user['id'],
                'Name'  => $user['name'],
                'eMail' => $user['email'],
                'Aktionen' => $operations,
            );
            array_push($tableData, $arr);
        }

        $viewModel = new ViewModel(array(
            'datatableHelper' => $this->datatableHelper,
            'controller' => $this->controller,
            'allowance' => $allowance,
            'profiles' => $tableData,
            'hidden_columns' => $hidden_columns,
        ));
        return $viewModel;
    }

    public function profileAction ($user_id)
    {
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
