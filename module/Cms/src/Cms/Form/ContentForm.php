<?php

namespace Cms\Form;

use Zend\Form\Form;

class ContentForm extends Form
{
     public function __construct($name = null, $options = array())
     {
         parent::__construct($name, $options);

         $this->add(array(
             'name' => 'post-fieldset',
             'type' => 'Cms\Form\ContentFieldSet',
             'options' => array(
                 'use_as_base_fieldset' => true
             )
         ));

         $this->add(array(
             'type' => 'submit',
             'name' => 'submit',
             'attributes' => array(
                 'value' => 'Create'
             )
         ));
     }
}