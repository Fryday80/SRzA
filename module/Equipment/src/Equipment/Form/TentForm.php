<?php
namespace Equipment\Form;

use Auth\Service\UserService;
use Equipment\Model\EnumTentShape;
use Equipment\Service\EquipmentService;
use Zend\Form\Form;

class TentForm extends Form
{
    /** @var  UserService */
    private $userService;
    /** @var EquipmentService  */
    private $equipService;

    public function __construct(EquipmentService $equipmentService, UserService $userService)
    {
        $this->equipService = $equipmentService;
        $this->userService = $userService;
        parent::__construct("Tent");
        $this->setAttribute('method', 'post');
        
        $this->add(array(
            'name' => 'id',
            'type' => 'hidden'
        ));

        $this->add(array(
            'name' => 'name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Bezeichnung',
            ),
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
        // length int
        $this->add(array(
            'name' => 'length',
            'type' => 'Number',
            'options' => array (
                'label' => 'Breite in Zentimeter',
            ),
        ));
        // width int
        $this->add(array(
            'name' => 'width',
            'type' => 'Number',
            'options' => array (
                'label' => 'Tiefe in Zentimeter',
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
        $this->add(array(
            'name' => 'color1',
            'type' => 'Zend\Form\Element\Color',
            'required' => true,
            'options' => array(
                'label' => 'Farbe1',
            ),
            'attributes' => array(
                'value' => '#FAEBd7',
            ),
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
            ),
            'attributes' => array(
                'value' => '#FAEBd7',
            ),
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

    private function getUsersForSelect()
    {
        $list = $this->userService->getUserIdUserNameList();
        $list[0] = 'Verein';
        return $list;
    }
    private function getShapesForSelect()
    {
        $list = array();
        foreach (EnumTentShape::TRANSLATION as $key => $value) {
            $list[$key] = $value;
        }
        ksort($list);
        return $list;
    }
    private function getTypesForSelect()
    {
        $list = $this->equipService->getTypeIDTypeNameList();
        $list[0] = array(
                'label' => 'Sonstige',
                'value' => 0,
                'selected' => true,
        );
        ksort($list);
        return $list;
    }

}