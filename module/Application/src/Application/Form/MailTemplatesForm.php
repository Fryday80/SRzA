<?php
namespace Application\Form;

use Application\Form\Filter\MailTemplatesFilter;
use Zend\Form\Form;

class MailTemplatesForm extends Form
{

    public function __construct()
    {
        parent::__construct("MailTemplates");
        $this->setAttribute('method', 'post');
        $this->setInputFilter(new MailTemplatesFilter());

        $this->add(array(
            'name' => 'id',
            'type' => 'text',
            'options' => array(
                'label' => 'Template'
            )
        ));
        $this->add(array(
            'name' => 'sender',
            'type' => 'text',
            'options' => array(
                'label' => 'Absender'
            )
        ));
        $this->add(array(
            'name' => 'subject',
            'type' => 'text',
            'options' => array(
                'label' => 'Betreff'
            )
        ));
        $this->add(array(
            'name' => 'msg',
            'type' => 'text',
            'options' => array(
                'label' => 'Nachricht'
            )
        ));
        $this->add(array(
            'name' => 'build_in',
            'type' => 'Hidden'
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
}