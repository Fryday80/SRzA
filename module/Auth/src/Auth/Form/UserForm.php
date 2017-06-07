<?php
namespace Auth\Form;

use Zend\Form\Element\Date;
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
        $fields[] = array(
            'name' => 'id',
            'type' => 'Hidden',
        );

        $fields[] = array(
            'name' => 'email',
            'type' => 'Text',
            'options' => array(
                'label' => 'eMail',
            )
        );

        $fields[] = array(
            'name' => 'name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Benutzername',
            ),
        );

        $fields[] = array(
            'name' => 'password',
            'type' => 'Password',
            'options' => array(
                'label' => 'Password',
                )
            );

        $fields[] = array(
            'name'       => 'passwordConfirm',
            'type'       => 'Password',
            'options' => array(
                'label' => 'Password confirm',
                )
            );

        $fields[] = array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
            ),
        );

        $fields[] = array(
            'name' => 'real_name',
            'type' => 'text',
            'attributes' => array(),
            'options' => array(
                'label' => 'Vorname',
            )
        );

        $fields[] = array(
            'name' => 'real_surename',
            'type' => 'text',
            'attributes' => array(),
            'options' => array(
                'label' => 'Nachname',
            )
        );

        $fields[] = array(
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

        $fields[]= array(
            'name' => 'birthday',
            'type' => 'Zend\Form\Element\Date',
            'options' => array(
                'label' => 'Geburtstag',
            ),
        );

        $fields[] = array(
            'name' => 'street',
            'type' => 'Text',
            'options' => array(
                'label' => 'Strasse',
            ),
        );

        $fields[] = array(
            'name' => 'city',
            'type' => 'Text',
            'options' => array(
                'label' => 'Stadt',
            ),
        );

        $fields[] = array(
            'name' => 'zip',
            'type' => 'Zend\Form\Element\Number',
            'options' => array(
                'label' => 'Postleitzahl',
            ),
        );

        foreach ($fields as $field){
            $this->add(
                $field,
                $this->getPriority($field['name'])
            );
        }

        //only for users with admin permission
        if($roles) {
            $adminFields[] = array(
                'name' => 'status',
                'type' => 'checkbox',
                'options' => array(
                    'label' => 'Aktiv',
                ),
            );

            $adminFields[] = array(
                'name' => 'role_id',
                'type' => 'Zend\Form\Element\Select',
                'attributes' => array(),
                'options' => array(
                    'label' => 'role',
                    'value_options' => $this->getRolesForSelect(),
                )
            );

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