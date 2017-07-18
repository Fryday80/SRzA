<?php
namespace Equipment\Form;

use Application\Form\MyForm;
use Auth\Service\UserService;
use Equipment\Model\Enums\ETentShape;
use Equipment\Model\Enums\ETentType;
use Equipment\Service\EquipmentService;

class TentForm extends MyForm
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
                'value_options' => ETentType::TRANSLATE_TO_STRING,
            ),
        ));
        // length int
        $this->add(array(
            'name' => 'width',
            'type' => 'Number',
            'options' => array (
                'label' => 'Breite in Zentimeter, Durchmesser wenn Rund',
            ),
        ));
        // width int
        $this->add(array(
            'name' => 'depth',
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
        foreach (ETentShape::TRANSLATION as $key => $value) {
            $list[$key] = $value;
        }
        ksort($list);
        return $list;
    }

    public function prepareDataForSetData($data)
    {

        if ($data['shape'] == ETentShape::ROUND)
            $data['width'] = $data['depth'];
        return $data;
    }
}