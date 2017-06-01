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

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));

        if($roles){
            $this->add(array(
                'name' => 'member_number',
                'type' => 'Text',
                'options' => array(
                    'label' => 'Mitgliedsnummer',
                ),
            ));
        }

        $this->add(array(
            'name' => 'email',
            'type' => 'Text',
            'options' => array(
                'label' => 'eMail',
                ),
            ),
            array(
                'priority' => 10, // Increase value to move to top of form
            ));

        $this->add(array(
            'name' => 'name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Benutzername',
                ),
            ),
            array(
                'priority' => 10, // Increase value to move to top of form
            ));

        if($roles){
            $this->add(array(
                'name' => 'real_name',
                'type' => 'text',
                'attributes' => array(),
                'options' => array(
                    'label' => 'Vorname',
                )
            ));
        }
        if($roles){
            $this->add(array(
                'name' => 'real_surename',
                'type' => 'text',
                'attributes' => array(),
                'options' => array(
                    'label' => 'Nachname',
                )
            ));
        }
        if($roles){
            $this->add(array(
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
            ));
        }

        $this->add(array(
            'name' => 'password',
            'type' => 'Password',
            'options' => array(
                'label' => 'Password',
                )
            ),
            array(
                'priority' => 2, // Increase value to move to top of form
            ));
        
        $this->add(array(
            'name'       => 'passwordConfirm',
            'type'       => 'Password',
            'options' => array(
                'label' => 'Password confirm',
                )
            ),
            array(
                'priority' => 2, // Increase value to move to top of form
            ));

        if($roles){
            $this->add(array(
                'name' => 'birthday',
                'type' => 'Text',
                'options' => array(
                    'label' => 'Geburtstag',
                ),
            ));
        }

        if($roles){
            $this->add(array(
                'name' => 'street',
                'type' => 'Text',
                'options' => array(
                    'label' => 'Strasse',
                ),
            ));
        }
        if($roles){
            $this->add(array(
                'name' => 'city',
                'type' => 'Text',
                'options' => array(
                    'label' => 'Stadt',
                ),
            ));
        }
        if($roles){
            $this->add(array(
                'name' => 'zip',
                'type' => 'Text',
                'options' => array(
                    'label' => 'Postleitzahl',
                ),
            ));
        }

        if ($roles) {
            $this->add(array(
                'name' => 'status',
                'type' => 'checkbox',
                'options' => array(
                    'label' => 'Aktiv',
                ),
            ),
                array(
                    'priority' => 11, // Increase value to move to top of form
                ));
        }
        if ($roles) {
            $this->add(array(
                'name' => 'role_id',
                'type' => 'Zend\Form\Element\Select',
                'attributes' => array(),
                'options' => array(
                    'label' => 'role',
                    'value_options' => $this->getRolesForSelect(),
                )
            ),
            array(
                'priority' => 2, // Increase value to move to top of form
            ));
        }
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
                ),
            ),
            array(
                'priority' => 1, // Increase value to move to top of form
            ));
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
}