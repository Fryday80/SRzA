<?php
namespace Auth\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Form\Element\Csrf;
use Auth\Model\RoleTable;
use Auth\Form\Filter\RoleFilter;

class RoleForm extends Form
{

    private $roleTable;

    public function __construct(RoleTable $roleTable)
    {
        $this->roleTable = $roleTable;
        
        parent::__construct("Role");
        $this->setAttribute('method', 'post');
        $this->setInputFilter(new RoleFilter());

        $this->add(array(
            'name' => 'rid',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'role_name',
            'type' => 'text',
            'attributes' => array(
                'id' => 'role_name',
                'class' => 'input-sm'
            ),
            'options' => array(
                'label' => 'Role'
            )
        ));
        $this->add(array(
            'name' => 'role_parent',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'role_parent',
                'class' => 'input-sm'
            ),
            'options' => array(
                'label' => 'Parent',
                'value_options' => $this->getRolesForSelect()
            )
        ));
        
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Submit',
                'class' => 'btn btn-primary'
            )
        ));
    }

    public function getRolesForSelect()
    {
        $roles = $this->roleTable->getUserRoles();
        
        $selectData = array();
        
        $selectData[0] = 'no parent';
        foreach ($roles as $res) {
            $selectData[$res['rid']] = $res['role_name'];
        }
        return $selectData;
    }
}