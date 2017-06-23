<?php
namespace Auth\Form;

use Zend\Form\Form;
use Auth\Form\Filter\ResourceFilter;

class ResourceForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('resource');
        $this->setInputFilter(new ResourceFilter());
        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'resource_name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Resource',
            ),
            'attributes' => array(
                'autofocus' => 'autofocus',
            ),
        ));
        $this->add(array(
            'name' => 'permissions',
            'type' => 'Text',
            'options' => array(
                'label' => 'Permissions',
            ),
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