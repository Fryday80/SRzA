<?php
namespace Equipment\Form;

use Auth\Service\UserService;
use Equipment\Service\EquipmentService;
use Zend\Form\Form;

class EquipmentForm extends Form
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

        // userId int
        $this->add(array(
            'name' => 'userId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array (
                'label' => 'Besitzer',
                'value_options' => $this->getUsersForSelect(),
            ),
        ));

        $this->add(array(
            'name' => 'name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Bezeichnung',
            ),
        ));

        $this->add(array(
            'name' => 'description',
            'type' => 'Textarea',
            'options' => array(
                'label' => 'Bechreibung',
            ),
        ));
        $this->add(array(
            'name' => 'image1',
            'type' => 'file',
            'options' => array(
                'label' => 'Bild 1',
            ),
            'attributes' => array(
                'accept' => 'image/*'
            )
        ));
        $this->add(array(
            'name' => 'image2',
            'type' => 'file',
            'options' => array(
                'label' => 'Bild 2',
            ),
            'attributes' => array(
                'accept' => 'image/*'
            )
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
        $this->add(array(
            'name' => 'sitePlannerObject',
            'type' => 'Zend\Form\Element\Checkbox',
            'required' => false,
            'attributes' => array(
                'data-toggle' => 'sitePlannerImage',
            ),
            'options' => array(
                'label' => 'Site Planner Object',
            ),
        ));
        //was wird zu html attributen?
        $this->add(array(
            'name' => 'sitePlannerImage',
            'type' => 'Zend\Form\Element\Radio',
            'required' => false,
            'attributes' => array(
                'data-toggleGrp' => 'sitePlannerImage',
                'value' => 0,
            ),
            'options' => array(
                'label' => 'Site Planner Bild',
                'value_options' => array(
                    0 => 'Bild 1',
                    1 => 'Bild 2',
                    array(
                        'value' => 2,
                        'label' => '(Zeichnung)',
                        'attributes' => array(
                            'data-toggle' => 'details',
                        ),
                    )
                ),
            ),
        ));
        $this->add(array(
            'name' => 'color',
            'type' => 'Zend\Form\Element\Color',
            'required' => true,
            'attributes' => array(
                'data-toggleGrp' => 'details',
                'value' => '#FAEBd7',
            ),
            'options' => array(
                'label' => 'Farbe1',
            ),
        ));
        $this->add(array(
            'name' => 'shape',
            'type' => 'select',
            'attributes' => array(
                'data-toggleGrp' => 'details',
            ),
            'options' => array(
               'label' => 'Form bei Zeichnung',
                'value_options' => array(
                    0 => 'Rund',
                    1 => 'Eckig'
                ),
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

}