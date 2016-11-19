<?php
namespace Usermanager\Controller;

use Zend\Http\Header\Referer;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Usermanager\Form\ProfileForm;

class UsermanagerController extends AbstractActionController
{
    private $controller = 'usermanager';

    private $editors_array = array ( 'Administrator', 'Profiladmin');

    private $whoAmI = array();
    /* @var $userTable \Auth\Model\User */
    private $userTable;

    private $profileService;

    private $datatableHelper;


    public function __construct($userTable, $accessService, $profileService)
    {
        $this->userTable = $userTable;

        $this->whoAmI['role'] = $accessService->getRole();
        $this->whoAmI['user_id'] = $accessService->getUserID();

        $this->profileService = $profileService;

        $this->datatableHelper = 'fake';
    }

    public function indexAction()
    {
        $allowance = $this->getAllowance();
        //$allowance = 'not set';

        $operations = array();

        $users = $this->userTable->getUsers()->toArray();
        $tableData = array();
        $hidden_columns = array ('id');
        foreach ($users as $user) {
            $operations = '<a href="/usermanager/profile/' . $user['id'] . '">Auswählen</a>';

            if ($allowance == 'editor') {
                $operations .=  '<a href="/usermanager/delete/' . $user['id'] . '">Löschen</a>';
            }
            $arr = array(
                'id'    => $user['id'],
                'Name'  => $user['name'],
                'eMail' => $user['email'],
                'Aktionen' => $operations,
            );
            array_push($tableData, $arr);
        }

        return new ViewModel(array(
            'allowance' => $allowance,
            'profiles' => $tableData,
            'hidden_columns' => $hidden_columns,
        ));
    }

    public function profileAction ()
    {
        $data_set = array ();
        $id = (int) $this->params()->fromRoute('id', 0);

        $allowance = $this->getAllowance($id);
        //$allowance = 'self';
        //$allowance = 'not set';

        $form = new ProfileForm();

        $user = $this->userTable->getUser($id);
        array_push($data_set, $user);

        // fry andere Profil Daten
        // array_push($data_set, $table_data);

        $this->dataToForm($data_set, $form);
        
        if ($allowance !== 'not set')
        {
            $this->addChangeSubmit($form);
            $form->add(array(
                'name' => 'change_password',
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Passwort ändern'
            ))  );
            if ($allowance == 'editor')
            {
                $this->addDeleteSubmit($form);
            }
        }

        return new ViewModel(array(
            'datatableHelper' => $this->datatableHelper,
            'controller' => $this->controller,
            'allowance' => $allowance,
            'form' => $form
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

    public function getElementsArray($form){
        $return = array();
        foreach ($form->getElements() as $element){
            $data = $element->getAttributes('name');
            array_push($return, $data['name']);
        }
        return $return;
    }

    public function getAllowance ($id = NULL)
    {   //salt n fry Roles zuteilen
        if (in_array($this->whoAmI['role'], $this->editors_array)) return 'editor';

        if ($id !== NULL && $id == $this->whoAmI['user_id']) return 'self';

        return 'not set';
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

    private function addChangeSubmit($form){
        $form->add(array(
            'name' => 'change',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Änderungen speichern',
            ),
        ));
    }

    private function addDeleteSubmit($form){
        $form->add(array(
            'name' => 'delete',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Löschen',
            ),
        ));
    }
}
