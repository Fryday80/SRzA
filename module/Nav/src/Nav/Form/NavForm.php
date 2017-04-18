<?php
namespace Nav\Form;

use Zend\Form\Form;

class NavForm extends Form
{

    public function __construct(Array $allRoles)
    {
        parent::__construct('Nav');
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
            'name' => 'target',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
            ),
            'options' => array(
                'label' => 'Target',
                'value_options' => array(
                    '_self' => 'Selbes fenster',
                    '_blank' => 'Neues Fenster'
                ),
            )
        ));
        $this->add(array(
            'name' => 'min_role_id',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
            ),
            'options' => array(
                'label' => 'permission',
                'value_options' => $allRoles,
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
}
