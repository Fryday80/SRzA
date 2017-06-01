<?php
namespace Auth\Form\Element;

use Zend\Form\Element\Select;

class RoleSelectElement extends Select
{
    public function init() {
        //list all roles
        $roleTable = $this->getServiceLocator()->get("Auth\Model\RoleTable");
        $roles = $roleTable->getUserRoles();
        
        $this->addMultiOption(0, 'Please select...');
        foreach ($roles as $role) {
            $this->addMultiOption($role['rid'], $role['role_name']);
        }
    }
}

