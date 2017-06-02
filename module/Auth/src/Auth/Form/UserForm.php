<?php
namespace Auth\Form;

use Zend\Form\Form;
use Auth\Form\Filter\UserFilter;

class UserForm extends Form
{
    private $allRoles;
    private $roles;
    private $role;
    public function __construct($roles = null, $role = null)
    {
        parent::__construct('User');
        $this->allRoles = $roles;
        $this->role = $role;
        $this->setAttribute('method', 'post');
        $this->setInputFilter(new UserFilter());

        $fields = array();
        $adminFields = array(); // only shown for admins
        //data that is usual in $this->add();
        $new = array(
            'name' => 'id',
            'type' => 'Hidden',
        );
        array_push($fields, $new);

        $new = array(
            'name' => 'member_number',
            'type' => 'Text',
            'options' => array(
                'label' => 'Mitgliedsnummer',
            )
        );
        array_push($adminFields, $new);

        $new = array(
            'name' => 'email',
            'type' => 'Text',
            'options' => array(
                'label' => 'eMail',
            )
        );
        array_push($fields, $new);

        $new = array(
            'name' => 'name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Benutzername',
            ),
        );
        array_push($fields, $new);

        $new = array(
            'name' => 'real_name',
            'type' => 'text',
            'attributes' => array(),
            'options' => array(
                'label' => 'Vorname',
            )
        );
        array_push($adminFields, $new);

        $new = array(
            'name' => 'real_surename',
            'type' => 'text',
            'attributes' => array(),
            'options' => array(
                'label' => 'Nachname',
            )
        );
        array_push($adminFields, $new);

        $new = array(
            'name' => 'gender',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(),
            'options' => array(
                'label' => 'Geschlecht',
                'value_options' => array(
                    'm' => 'Mann',
                    'f' => 'Frau',
                ),
            )
        );
        array_push($adminFields, $new);

        $new = array(
            'name' => 'password',
            'type' => 'Password',
            'options' => array(
                'label' => 'Password',
                )
            );
        array_push($fields, $new);

        $new = array(
            'name'       => 'passwordConfirm',
            'type'       => 'Password',
            'options' => array(
                'label' => 'Password confirm',
                )
            );
        array_push($fields, $new);

        $new = array(
            'name' => 'birthday',
            'type' => 'Text',
            'options' => array(
                'label' => 'Geburtstag',
            ),
        );
        array_push($adminFields, $new);

        $new = array(
            'name' => 'street',
            'type' => 'Text',
            'options' => array(
                'label' => 'Strasse',
            ),
        );
        array_push($adminFields, $new);

        $new = array(
            'name' => 'city',
            'type' => 'Text',
            'options' => array(
                'label' => 'Stadt',
            ),
        );
        array_push($adminFields, $new);

        $new = array(
            'name' => 'zip',
            'type' => 'Text',
            'options' => array(
                'label' => 'Postleitzahl',
            ),
        );
        array_push($adminFields, $new);

        $new = array(
            'name' => 'status',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'Aktiv',
                ),
            );
        array_push($adminFields, $new);

        $new = array(
            'name' => 'role_id',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(),
            'options' => array(
                'label' => 'role',
                'value_options' => $this->getRolesForSelect(),
            )
        );
        array_push($adminFields, $new);

        $new = array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
                ),
            );
        array_push($fields, $new);

        foreach ($fields as $field){
                $this->add(
                    $field,
                    $this->getPriority($field['name'])
                );
        }

        if($roles){
            foreach ($adminFields as $field){
                $this->add(
                    $field,
                    $this->getPriority($field['name'])
                );
            }
        }

    }
    private function getRolesForSelect() {
        if (!$this->roles) {
            $this->roles = [];
            $this->getParents($this->allRoles, $this->role);
            $this->roles = array_reverse($this->roles);
        }
        $selectData = array();

        foreach ($this->roles as $role) {
            $selectData[$role['rid']] = $role['role_name'];
        }
        return $selectData;
    }

    private function getParents($data, $role) {
        foreach ($data as $roleData) {
            if ($roleData['role_name'] == $role) {
                array_push($this->roles, $roleData);
                $this->getParents($data, $roleData['role_parent_name'], $this->roles);
            }
        }
    }
    private function getPriority($name){
        //high number means top position
        $order = array (
            'id' => 100,
            'status' => 25,
            'role_id'=>24,
            'member_number' => 23,
            'name' => 20,
            'email' => 19,
            'password' => 18,
            'passwordConfirm' => 17,
            'real_name' => 15,
            'real_surename' => 14,
            'gender' => 13,
            'birthday' => 12,
            'street' => 10,
            'city' => 9,
            'zip' => 8,
            'submit' => 1
        );
        if (!isset ($order[$name]) ){
            $prio = 7;
        } else {
            $prio = $order[$name];
        }
        return array('priority' => $prio);
    }
}