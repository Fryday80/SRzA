<?php
namespace Cast\Form;

use Zend\Form\Form;

class JobForm extends Form
{
    public function __construct()
    {
        parent::__construct("Job");
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'job',
            'type' => 'text',
            'options' => array(
                'label' => 'Job Name'
            )
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Submit',
            )
        ));
    }
}