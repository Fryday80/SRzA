<?php
namespace Equipment\Form;

use Equipment\Service\TentService;
use Zend\Form\Form;

class TentColorsForm extends Form
{
    /** @var TentService  */
    private $tentService;

    public function __construct(TentService $tentService)
    {
        $this->tentService = $tentService;
        parent::__construct("TentColors");
        $this->setAttribute('method', 'post');
        
        $this->add(array(
            'name' => 'id',
            'type' => 'hidden'
        ));
        // userId int
        $this->add(array(
            'name' => 'name',
            'type' => 'Text',
            'options' => array (
                'label' => 'Bezeichnung',
            ),
        ));
/// Color
        $this->add(array(
            'name' => 'color1',
            'type' => 'Zend\Form\Element\Color',
            'required' => true,
            'options' => array(
                'label' => 'Farbe1',
            )
        ));

        $this->add(array(
            'name' => 'biColor',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array (
                'label' => 'Zweifarbig',
            ),
        ));
/// Color
        $this->add(array(
            'name' => 'color2',
            'type' => 'Zend\Form\Element\Color',
            'required' => true,
            'options' => array(
                'label' => 'Farbe2',
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