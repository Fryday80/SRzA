<?php
namespace Nav\Form;

use Auth\Model\RoleTable;
use Zend\Form\Form;

class NavForm extends Form
{
    /** @var  $roleTable RoleTable */
    private $allRoles;
    private $cached = false;

    public function __construct(Array $allRoles)
    {
        parent::__construct('Nav');
        $this->allRoles = $allRoles;
        //$this->setInputFilter(new ResourceFilter());
        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'menu_id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'label',
            'type' => 'Text',
            'options' => array(
                'label' => 'Label',
            ),
        ));
        $this->add(array(
            'name' => 'uri',
            'type' => 'Text',
            'options' => array(
                'label' => 'Url',
            ),
        ));
        $this->add(array(
            'name' => 'permission_id',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
            ),
            'options' => array(
                'label' => 'permission',
                'value_options' => $this->getPermissionsForSelect(),
            )
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
            ),
        ));
    }
    public function getPermissionsForSelect()
    {
        $selectData = array();
        $megalomaniac = 'Administrator';
        $rearranged = array();
        $hash = array();
        $return = array();

        if (!$this->cached) {
            foreach ($this->allRoles as $key => $role) {
                $rearranged[$role['role_name']] = $role;
                $hash[$role['rid']] = $role['role_name'];
            }
            $return[count($rearranged)] = $rearranged[$megalomaniac];

            for ($i = count($rearranged)-1; $i > 0; $i--) {
                $return[$i-1] = $rearranged [ $hash[$i] ];
            }

            foreach ($return as $role) {
                $selectData[$role['rid']] = $role['role_name'];
            }
            $this->allRoles = $selectData;
//  @todo implement cache ---> $this->cached = true;
        }
        bdump($this->allRoles);

        return $this->allRoles;
    }
}
