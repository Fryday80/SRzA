<?php
namespace Nav\Form;

use Zend\Form\Form;

class NavForm extends Form
{
    public function __construct(Array $permTable)
    {
        parent::__construct('Nav');
        $this->permTable = $permTable;
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
            'name' => 'route',
            'type' => 'Text',
            'options' => array(
                'label' => 'Route',
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

        foreach ($this->permTable as $res) {
            $selectData[$res['id']] = $res['resource_name'] . ' - ' . $res['permission_name'];
        }
        return $selectData;
    }
}
