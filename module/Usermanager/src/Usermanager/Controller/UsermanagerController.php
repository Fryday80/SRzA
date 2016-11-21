<?php
namespace Usermanager\Controller;

use Zend\Http\Header\Referer;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Usermanager\Form\ProfileForm;
use Usermanager\Form\ConfirmForm;

class UsermanagerController extends AbstractActionController
{
    private $editors_array = array ( 'Administrator', 'Profiladmin');
    private $allowance_result_options = array ('not set', 'editor', 'self');

    private $whoAmI = array();
    /* @var $userTable \Auth\Model\User */
    private $userTable;

    private $profileService;

    private $accessService;

    private $owner;


    public function __construct($userTable, $accessService, $profileService)
    {
        $this->userTable = $userTable;

        $this->accessService = $accessService;

        $this->whoAmI['role'] = $accessService->getRole();
        $this->whoAmI['user_id'] = $accessService->getUserID();

        $this->profileService = $profileService;
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

            if ($this->accessService->allowed("Usermanger\Controller\UsermanagerController", "edit")) {
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
        if ($this->accessService->allowed("Usermanger\Controller\UsermanagerController", "edit")) {
            $addButton = '<a href="/usermanager/add">Mitglied hinzufügen</a>';
        }

        return new ViewModel(array(
            'jsOptions' => $this->setJSOptionForDatatables($allowance),
            'profiles' => $tableData,
            'hidden_columns' => $hidden_columns,
            'addButton' => $addButton,
        ));
    }

    public function profileAction ()
    {
        $data_set = array ();
        $id = (int) $this->params()->fromRoute('id', 0);
        $this->owner = $this->accessService->getUserID() === $id;

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
            'owner' => $this->owner,
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

    public function getAllowance ($id = NULL)
    {   //salt n fry Roles zuteilen

        if ($id !== NULL && $id == $this->whoAmI['user_id']) return 'self';
        
        if (in_array($this->whoAmI['role'], $this->editors_array)) return 'editor';

        return 'not set';
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

    private function getElementsArray($form)
    {
        $return = array();
        foreach ($form as $element){
            $data = $element->getAttributes('name');
            array_push($return, $data['name']);
        }
        return $return;
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
