<?php
namespace Cast\Form;

use Zend\Form\Element\File;
use Zend\Form\Form;
use Zend\InputFilter\FileInput;
use Zend\InputFilter\InputFilter;

class BlazonForm extends Form
{
    public function __construct()
    {
        parent::__construct("Blazon");
        $this->setAttribute('method', 'post');

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
            'name' => 'isOverlay',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'Overlay'
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
            'name' => 'offsetX',
            'type' => 'number',
            'options' => array(
                'label' => 'overlay abstand links'
            )
        ));
        $this->add(array(
            'name' => 'offsetY',
            'type' => 'number',
            'options' => array(
                'label' => 'overlay abstand rechts'
            )
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Submit',
            )
        ));
        $this->addInputFilter();
    }
    public function addInputFilter()
    {
        $inputFilter = new InputFilter();

        // blazon
        $blazonFileInput = new FileInput('blazon');
        $blazonFileInput->setRequired(false);
        $blazonFileInput->getFilterChain()->attachByName(
            'filerenameupload',
            array(
                'target'    => './temp/',
                'use_upload_name ' => true,
                'randomize' => true,
            )
        );
        $inputFilter->add($blazonFileInput);

        // blazonBig
        $bigBlazonFileInput = new FileInput('blazonBig');
        $bigBlazonFileInput->setRequired(false);
        $bigBlazonFileInput->getFilterChain()->attachByName(
            'filerenameupload',
            array(
                'target'    => './temp/',
                'use_upload_name ' => true,
                'randomize' => true,
            )
        );
        $inputFilter->add($bigBlazonFileInput);
        $this->setInputFilter($inputFilter);
    }
}