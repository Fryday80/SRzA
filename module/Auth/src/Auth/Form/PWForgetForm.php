<?php
namespace Auth\Form;

use Auth\Form\Filter\PWForgetFilter;
use Zend\Form\Form;

class PWForgetForm extends Form
{
    public function __construct()
    {
        parent::__construct('PWForget');
        $this->setAttribute('method', 'post');
        $this->setInputFilter(new PWForgetFilter());

        $this->add( array(
            'name' => 'password',
            'type' => 'Password',
            'options' => array(
                'label' => 'Neues Password',
            ),
            'attributes' => array(
                'autofocus' => 'autofocus',
            ),
            )
        );

        $this->add( array(
            'name'       => 'passwordConfirm',
            'type'       => 'Password',
            'options' => array(
                'label' => 'Password confirm',
                )
            )
        );

        $this->add( array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
                ),
            )
        );
    }
    private function getPriority($name){
        //high number means top position
        $order = array (
            'id' => 100,
            'password' => 18,
            'passwordConfirm' => 17,
            'submit' => 1
        );
        if (!isset ($order[$name]) ){
            $prio = 7;
        } else {
            $prio = $order[$name];
        }
        return array('priority' => $prio);
    }
}