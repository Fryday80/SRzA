<?php
namespace Auth\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Form\Element\Csrf;
use Auth\Model\Tables\RoleTable;
use Auth\Model\Tables\PermissionTable;

class PermissionDeleteForm extends Form
{
    private $permTable;
    
    public function __construct(Array $permTable)
    {
        parent::__construct("PermissionDelete");
        
        $this->permTable = $permTable;
        $this->setAttribute('method', 'post');
        
        $this->add(array(
            'name' => 'role_id',
            'type' => 'hidden',
        ));
        $this->add(array(
            'name' => 'role_permission_id',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'size' => 20,
                'multiple' => 'multiple',
                'autofocus' => 'autofocus',
            ),
            'options' => array(
                'value_options' => $this->getPermissionsForSelect(),
            )
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'loginCsrf',
            'options' => array(
                'csrf_options' => array(
                    'timeout' => 3600
                )
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

    public function getPermissionsForSelect()
    {
        $selectData = array();
        foreach ($this->permTable as $res) {
            $selectData[$res['id']] = array(
                'label' =>  $res['resource_name'] . ' - ' . $res['permission_name'],
                'disabled' => (isset($res['disabled']))? true : false,
                'value' => $res['id']
            );
            
        }
        return $selectData;
    }
}