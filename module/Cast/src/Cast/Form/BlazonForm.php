<?php
namespace Cast\Form;

use Cast\Form\Filter\BlazonFilter;
use Zend\Form\Form;

class BlazonForm extends Form
{
    public function __construct($filterFlag = null)
    {
        parent::__construct("Blazon");
        $this->setAttribute('method', 'post');
        $this->setInputFilter(new BlazonFilter($filterFlag));

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'name',
            'type' => 'text',
            'options' => array(
                'label' => 'Wappen Name'
            )
        ));
        $this->add(array(
            'name' => 'blazon',
            'type' => 'file',
            'options' => array(
                'label' => 'Wappen',
            ),
            'attributes' => array(
                'accept' => 'image/*'
            )
        ));
        $this->add(array(
            'name' => 'blazonBig',
            'type' => 'file',
            'options' => array(
                'label' => 'Großes Wappen',
            ),
            'attributes' => array(
                'accept' => 'image/*'
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