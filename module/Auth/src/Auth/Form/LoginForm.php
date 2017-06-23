<?php
namespace Auth\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Form\Element\Csrf;
use Auth\Form\Filter\LoginFilter;

class LoginForm extends Form
{

    public function __construct($name)
    {
        parent::__construct($name);
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '/login');
        $this->setInputFilter(new LoginFilter());
        $this->add(array(
            'name' => 'email',
            'type' => 'text',
            'attributes' => array(
                'autofocus' => 'autofocus',
                'placeholder' => 'example@example.com',
            ),
            'options' => array(
                'label' => 'Email',
            )
        ));
        
        $this->add(array(
            'name' => 'password',
            'type' => 'password',
            'attributes' => array(
                'placeholder' => '**********'
            ),
            'options' => array(
                'label' => 'Password',                
            )
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'loginCsrf',
            'options' => array(
                'csrf_options' => array(
                    'timeout' => 3600
                )
            )
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

    public function unFocus()
    {
        $this->get('email')->setAttribute('autofocus', false);
    }
}
