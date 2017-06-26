<?php
namespace Equipment\Form;

use Auth\Service\UserService;
use Equipment\Model\EnumTentShape;
use Equipment\Service\TentService;
use Zend\Form\Form;

class TentForm extends Form
{
    /** @var  UserService */
    private $userService;
    /** @var TentService  */
    private $tentService;

    public function __construct(TentService $tentService, UserService $userService)
    {
        $this->tentService = $tentService;
        $this->userService = $userService;
        parent::__construct("Tent");
        $this->setAttribute('method', 'post');
        
        $this->add(array(
            'name' => 'id',
            'type' => 'hidden'
        ));
        // userId int
        $this->add(array(
            'name' => 'userId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array (
                'label' => 'Besitzer',
                'value_options' => $this->getUsersForSelect(),
            ),
        ));
        // shape int
        $this->add(array(
            'name' => 'shape',
            'type' => 'Zend\Form\Element\Select',
            'options' => array (
                'label' => 'Form',
                'value_options' => $this->getShapesForSelect(),
            ),
        ));
        // type int
        $this->add(array(
            'name' => 'type',
            'type' => 'Zend\Form\Element\Select',
            'options' => array (
                'label' => 'Typ',
                'value_options' => $this->getTypesForSelect(),
            ),
        ));
        // color int
        $this->add(array(
            'name' => 'color',
            'type' => 'Zend\Form\Element\Select',
            'options' => array (
                'label' => 'Farbe',
                'value_options' => $this->getColorsForSelect(),
            ),
        ));
        // length int
        $this->add(array(
            'name' => 'length',
            'type' => 'Number',
            'options' => array (
                'label' => 'Breite in Metern',
            ),
        ));
        // width int
        $this->add(array(
            'name' => 'width',
            'type' => 'Number',
            'options' => array (
                'label' => 'Tiefe in Metern',
            ),
        ));
        // spareBeds int
        $this->add(array(
            'name' => 'spareBeds',
            'type' => 'Number',
            'options' => array (
                'label' => 'Freie Schlafplätze',
            ),
        ));
        // showtent int
        $this->add(array(
            'name' => 'isShowTent',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array (
                'label' => 'Schauzelt',
            ),
        ));
        // groupEquip int
        $this->add(array(
            'name' => 'isGroupEquip',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array (
                'label' => 'Gruppeneigentum',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'options' => array (
                'label' => 'Go',
            ),

        ));
    }

    /**
     *
     */
    private function getUsersForSelect()
    {
        return $this->userService->getUserIdUserNameList();
    }
    private function getShapesForSelect()
    {
        $list = array();
        foreach (EnumTentShape::TRANSLATION as $key => $value) {
            $list[$key] = $value;
        }
        return $list;
    }
    private function getTypesForSelect()
    {
        return $this->tentService->getTypeIDTypeNameList();
    }
    private function getColorsForSelect()
    {
        return $this->tentService->getColorIDColorNameList();
    }

}