<?php
namespace Usermanager\Controller;

use Application\Utility\TablehelperConfig;
use Zend\Http\Header\Referer;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Usermanager\Form\ProfileForm;
use Usermanager\Form\ConfirmForm;
use Application\Utility\FormConfiguration;

class UsermanagerController extends AbstractActionController
{
    /* @var $userTable \Auth\Model\User */
    private $userTable;

    private $accessService;

    private $owner;


    public function __construct($userTable, $accessService)
    {
        $this->userTable = $userTable;

        $this->accessService = $accessService;
    }

    public function indexAction()
    {
        $addButton = '';
        $users = $this->userTable->getUsers()->toArray();
        foreach ($users as $key => $user) {
            if ($this->accessService->allowed("Usermanager\Controller\Usermanager", "delete")) {
                $users[$key]['allow_delete'] = true;
            }
        }

        if ($this->accessService->allowed("Usermanager\Controller\Usermanager", "edit")) {
            $addButton = '<a href="/usermanager/add">Mitglied hinzufügen</a>';
        }
        return new ViewModel(array(
            'jsOptions' => $this->setJSOptionForDatatables(),
            'profiles' => $users,
            'addButton' => $addButton,
        ));
    }

    public function profileAction ()
    {
        $form_config = new FormConfiguration();
        $form_config->setFieldConfig(array('type' => array ('select' => array('class' => 'select'))));
        $data_set = array ();
        $id = (int) $this->params()->fromRoute('id', 0);
        $this->owner = $this->accessService->getUserID() === $id;
        
        if ($this->owner || $this->accessService->allowed("Usermanger\Controller\UsermanagerController", "edit"))
        {
            $request = $this->getRequest();
        }
        if ($request->isPost())
        {
            $set_data = $request->getContent();
            $this->formToData($set_data);
        }

        

        $form = new ProfileForm( $this->accessService, $this->owner );
        $form->setAttribute('action', '/usermanager/profile/' . $id);

        $user = $this->userTable->getUser($id);     //--//
        array_push($data_set, $user);               //--//
                                                    //--// if no other table used
        // fry andere Profil Daten                  //--// $this->dataToForm($this->userTable->getUser($id), $form);
        // array_push($data_set, $table_data);      //--// aber vorher data to form um ein level kürzen!!
                                                    //--//
        $this->dataToForm($data_set, $form);        //--//

        return new ViewModel(array(
            'id' => $id,
            'owner' => $this->owner,
            'form' => $form,
            'form_config' => $form_config
        ));
    }

    public function addAction ()
    {
        if ($this->getAllowance() == 'not set')
            return $this->redirect()->toRoute('usermanager');
        $request = $this->getRequest();

        if ($request->isPost()) {
            $user_data = $request->getContent();
            $this->userTable->saveUser($user_data);
        }

        $form = new ProfileForm();
        $form->setAttribute('action', 'usermanager/add');
        return array(
            'form' => $form,
            'back' => '<a href="/usermanager">Abbrechen und zurück</a>',
        );
    }

    public function deleteAction ()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        $request = $this->getRequest();
        if (! $id && !$request->isPost()) {
            return $this->redirect()->toRoute('usermanager');
        }
        if ($request->isPost()) {
            $confirmed = $request->getPost('delete_confirm', 'no');
            if ($confirmed !== 'no') {
                $this->userTable->deleteUser($id);
                return $this->redirect()->toRoute('usermanager');
            }
            // Redirect to list of albums
            return $this->redirect()->toRoute('usermanager');
        }
        $form = new ConfirmForm();
        $form->get('id')->setAttribute('value', $id);
        $form->setAttribute('action', '/usermanager/delete/' . $id);

        try {
            $user_to_delete = $this->userTable->getUser($id);
        } catch (\Exception $ex) {
            print ($ex);
            return $this->redirect()->toRoute('/usermanager');
        }
        $message = 'User >>' . $user_to_delete->name . '<< mit der Mitgliedsnummer >>' . $user_to_delete->membernumber . '<< löschen';

        return array (
            'message' => $message,
            'form' => $form
        );
    }

    private function dataToForm($data_set, $form)
    {
        $ts_value = '';
        foreach ($data_set as $set)
        {
            $permission = $this->accessService->allowed("Usermanger\Controller\UsermanagerController", "edit");

            $timestamp_fields = array ('created_on', 'modified_on', 'birthday');

            foreach ($set as $key => $value){
                if (in_array($key, $this->getElementsArray($form)))
                {
                    if ($key == 'gender' && ($this->owner || $permission)){
                        $value = ($value == 'm') ? 'Mann' : 'Frau';
                    }
                    if (in_array($key, $timestamp_fields)){
                        if ($key == 'birthday') {
                            $ts_value = date ('d.m.Y', $value);
                        }
                        else
                        {
                            $value = date('d.m.Y', $value);
                        }
                    }
                    $ele = $form->get($key);
                    $ele->setValue($value);
                }
            }
            $ele = $form->get('birthday_dp');
            $ele->setValue($ts_value);
        }
    }

    private function formToData($data)
    {
        $ts_value = '';
        foreach ($data as $fields)
        {
            $date_fields = array ('created_on', 'modified_on');
            $datepicker_fields = array ('birthday');

            foreach ($data as $key => $value){
                if (in_array($key, $this->getElementsArray($form)))
                {
                    if ($key == 'gender' && ($this->owner || $permission)){
                        $value = ($value == 'm') ? 'Mann' : 'Frau';
                    }
                    if (in_array($key, $timestamp_fields)){
                        if ($key == 'birthday') {
                            $ts_value = date ('d.m.Y', $value);
                        }
                        else
                        {
                            $value = date('d.m.Y', $value);
                        }
                    }
                    $ele = $form->get($key);
                    $ele->setValue($value);
                }
            }
            $ele = $form->get('birthday_dp');
            $ele->setValue($ts_value);
        }
    }

    private function getElementsArray($form)
    {
        $return = array();
        foreach ($form as $element){
            $data = $element->getAttributes('name');
            array_push($return, $data['name']);
        }
        return $return;
    }

    
}
