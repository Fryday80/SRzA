<?php
namespace Cms\Form;

use Cms\Model\DataModels\Content;
use Zend\Form\Fieldset;
use Zend\Stdlib\Hydrator\ClassMethods;

class ContentFieldSet extends Fieldset
{

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
        
        $this->setHydrator(new ClassMethods(false));
        $this->setObject(new Content());
        
        $this->add(array(
            'type' => 'hidden',
            'name' => 'id'
        ));

        $this->add(array(
            'type' => 'text',
            'name' => 'title',
            'options' => array(
                'label' => 'Title'
            )
        ));

        $this->add(array(
            'type' => 'textarea',
            'name' => 'content',
            'options' => array(
                'label' => 'Content'
            )
        ));

        $this->add(array(
            'type' => 'text',
            'name' => 'url',
            'options' => array(
                'label' => 'Url'
            )
        ));

        $this->add(array(
            'type' => 'text',
            'name' => 'exceptedRoles',
            'options' => array(
                'label' => 'Excepted Roles'
            )
        ));

    }
}