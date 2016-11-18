<?php
namespace Usermanager\Controller;

use Zend\Http\Header\Referer;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Usermanager\Form\ProfileForm;

class UsermanagerController extends AbstractActionController
{
    private $controller = 'usermanager';

    private $editors_array = array ( 'administrator', 'editor');

    private $whoAmI = array();
    /* @var $userTable \Auth\Model\User */
    private $userTable;

    private $profileService;


    private $datatableHelper;


    public function __construct($userTable, $accessService, $profileService, $datatableHelper)
    {
        $this->userTable = $userTable;

        $this->whoAmI['role'] = $accessService->getRole();
        $this->whoAmI['user_id'] = $accessService->getUserID();

        $this->profileService = $profileService;

        $this->datatableHelper = $datatableHelper;
    }

    public function indexAction()
    {
        $allowance = $this->getAllowance($this->whoAmI['user_id']);
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

        return new ViewModel(array(
            'datatableHelper' => $this->datatableHelper,
            'controller' => $this->controller,
            'allowance' => $allowance,
            'profiles' => $tableData,
            'hidden_columns' => $hidden_columns,
        ));
    }

    public function profileAction ()
    {
        $data_set = array ();
        $id = (int) $this->params()->fromRoute('id', 0);
        $allowance = $this->getAllowance($this->whoAmI['user_id']);

        $form = new ProfileForm();

        $user = $this->userTable->getUser($id);
        array_push($data_set, $user);

        // fry andere Profil Daten
        // array_push($data_set, $table_data);

        $this->dataToForm($data_set, $form);
        
        if ($allowance == 'self')
        {
            $this->changeSubmit($form);
        }
        if (in_array($allowance, $this->editors_array))
        {
            $this->changeSubmit($form);
            $this->deleteSubmit($form);
        }

        return new ViewModel(array(
            'datatableHelper' => $this->datatableHelper,
            'controller' => $this->controller,
            'allowance' => $allowance,
            'form' => $form,
            'data_set' => $data_set,
        ));
    }

    public function deleteAction ()
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

    private function dataToForm($data_set, $form)
    {
        foreach ($data_set as $set)
        {
            foreach ($set as $key => $value){
                if (in_array($key, $this->getElementsArray($form)))
                {
                    $ele = $form->get($key);
                    $ele->setValue($value);
                }
            }
        }
    }

    private function getAllowance ($id = 0)
    {
        if ($id == 0)
        {
            if ($this->whoAmI['role'] == 'Administrator' || $this->whoAmI['role'] == 'Profiladmin') //salt n fry Roles zuteilen
            {
                return 'editor';
            }
            return;
        }
        if ($id !== 0){
            if ($this->whoAmI['role'] == 'Administrator' || $this->whoAmI['role'] == 'Profiladmin') //salt n fry Roles zuteilen
            {
                return 'editor';
            }
            if ($id == $this->whoAmI['user_id']){
                return 'self';
            }
        }
    }

    private function changeSubmit($form){
        $form->add(array(
            'name' => 'change',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Änderungen speichern',
            ),
        ));
    }

    private function deleteSubmit($form){
        $form->add(array(
            'name' => 'delete',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Löschen',
            ),
        ));
    }

    public function getElementsArray($form){
        $return = array();
        foreach ($form->getElements() as $element){
            $data = $element->getAttributes('name');
            array_push($return, $data['name']);
        }
        return $return;
    }
}
