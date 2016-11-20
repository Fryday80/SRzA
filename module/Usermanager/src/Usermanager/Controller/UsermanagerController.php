<?php
namespace Usermanager\Controller;

use Zend\Http\Header\Referer;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Usermanager\Form\ProfileForm;
use Usermanager\Form\ConfirmForm;

class UsermanagerController extends AbstractActionController
{
    private $controller = 'usermanager';

    private $editors_array = array ( 'Administrator', 'Profiladmin');
    private $allowance_result_options = array ('not set', 'editor', 'self');

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

        $operations = '';

        $users = $this->userTable->getUsers()->toArray();
        $tableData = array();
        $hidden_columns = array ('id');
        foreach ($users as $user) {
            $operations .= '<a href="/usermanager/profile/' . $user['id'] . '">Auswählen</a>';

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
            $operations ='';
        }

        return new ViewModel(array(
            'jsOptions' => $this->setJSOptionForDatatables($allowance),
            'profiles' => $tableData,
            'hidden_columns' => $hidden_columns,
            'addButton' => '<a href="/usermanager/add">Mitglied hinzufügen</a>',
        ));
    }

    public function profileAction ()
    {
        $data_set = array ();
        $id = (int) $this->params()->fromRoute('id', 0);

        $allowance = $this->getAllowance($id);
        //$allowance = 'self';     //cleanfix overrides for testing
        //$allowance = 'not set';

        $form = new ProfileForm();
        $form->setAttribute('action', '/usermanager/profile/' . $id);

        $user = $this->userTable->getUser($id);
        array_push($data_set, $user);

        // fry andere Profil Daten
        // array_push($data_set, $table_data);

        $this->dataToForm($data_set, $form);

        $this->makeUpForm($form);

        if ($allowance !== 'not set')
        {
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
            'id' => $id,
            'allowance' => $allowance,
            'form' => $form
        ));
    }

    public function addAction ()
    {
        if ($this->getAllowance() == 'not set')
            return $this->redirect()->toRoute('usermanager');
        $request = $this->getRequest();

        if ($request->isPost()) {
            $user_data = $request->getContent();
            dumpd ($user_data);
            $this->userTable->saveUser($user_data);
        }
        $form = new ProfileForm();
        $form->setAttribute('action', 'usermanager/add');
        $this->makeUpForm($form, 'self');
        /*
        $form->add(array(
            'name' => 'save',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Speichern'
            ),
        ));
*/
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

    public function getElementsArray($form){
        $return = array();
        foreach ($form as $element){
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
        $ts_value = '';
        foreach ($data_set as $set)
        {
            foreach ($set as $key => $value){
                if (in_array($key, $this->getElementsArray($form)))
                {
                    if ($key == 'gender'){
                        $value = ($value = 'm') ? 'Mann' : 'Frau';
                    }
                    if ($key == 'created_on'){
                       $value = date ('d.m.Y', $value);
                    }
                    if ($key == 'modified_on'){
                        $value = date ('d.m.Y', $value);
                    }
                    if ($key == 'birthday'){
                    $ts_value = date ('d.m.Y', $value);
                    }
                    $ele = $form->get($key);
                    $ele->setValue($value);
                }
            }
            $ele = $form->get('birthday_dp');
            $ele->setValue($ts_value);
        }
    }

    /**
     * @param $form the form designed to view
     * @param null $options either allowance... if not given allowance is get by method
     * || no other case or option neede till now
     */
    private function makeUpForm ($form, $options = NULL){
        $id = (int) $form->get('id')->getValue();
        if (in_array($options, $this->allowance_result_options)) {
            $allowance = $options;
        } else {
            $allowance = $this->getAllowance($id);
        }

        if ($allowance == 'self' || $allowance == 'editor') {
            $this->makeUpSelfSet($form);
        }

        if ($allowance == 'editor') {
            $this->makeUpEditorSet($form);
        }
    }

    private function makeUpSelfSet ($form){
        $this->addChangeSubmit($form);
    }

    private function makeUpEditorSet ($form){
        $form->add(array(
        'name' => 'status',
        'type' => 'Select',
        'attributes' => array(
            'class' => 'editor',
            'options' => array(
                'Y' => 'active',
                'N' => 'inactive'
            ),
        ),
        'options' => array(
            'label' => 'Status',
        ),
    ));
        $form->add(array(
            'name' => 'gender',
            'type' => 'Select',
            'attributes' => array(
                'options' => array(
                    'm' => 'Mann',
                    'f' => 'Frau'
                ),
            ),
            'options' => array (
                'label' => 'Geschlecht',
            ),
        ));
        $this->addDeleteSubmit($form);

        foreach ($form as $element) {
            $element->setAttributes(array('readonly' => 'false'));
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

    /**
     * @param $options mixed can be
     * left empty       -> returns standard setting ||
     * allowance string -> returns options set for a special allowance ||
     * options array   may look like array ('b' => '[settings]', 't')
     * @return string set of options for js plugin "datatables"
     */
    private function setJSOptionForDatatables($options = ''){
        if (is_array($options))
        {
            $allowed_options = array ('B','l','f','r','t','i','p');
            $js_dom = '"dom": "';
            $js_option = '';
            foreach ($options as $key => $option)
            {
                if (!is_array($option))
                {
                    $option = strtolower($option);
                    $js_dom .= ((in_array($option, $allowed_options)) && ($option !== 'b'))?:'B';
                }
                else
                {
                    $js_dom .= ((in_array($key, $allowed_options)) && ($key !== 'b'))?:'B';
                    $js_option .= $option . ',';
                }
            }
            $js_dom .= (stristr($js_dom, 't')) ? '' : 't';  //adds the t-able in the dom if not set
            $js_dom .= '",';
            $js_option = str_replace(',,', ',', $js_option);
            $js_optionset = $js_option . $js_dom;
            return $js_optionset;
            //array of the options b r f i p etc...
            // momentary no usage  //fry todo implement options selector
            return '"lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],';
        }
        else if (in_array($options, $this->allowance_result_options)) {
            if ($options == 'editor') {
                // return ;  // override because same options set
                $options = 'self';
            } if ($options == 'self') return '    "lengthMenu": [ [25, 10, 50, -1], [25, 10, 50, "All"] ],
                                                    buttons: [
                                                        "print", "copy", "csv", "excel", "pdf"
                                                    ],
                                                    select: {
                                                        style: "multi"
                                                    },
                                                    "dom": "Blfrtip",';
        }
        else return '"lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],';

            //https://datatables.net/reference/index for preferences/documentation
    }
}
