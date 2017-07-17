<?php
namespace Equipment\Form;

use Application\Form\MyForm;
use Auth\Service\UserService;
use Equipment\Model\Enums\EEquipSitePlannerImage;
use Equipment\Service\EquipmentService;

class EquipmentForm extends MyForm
{
    const EQUIPMENT_IMAGES_PATH = 'media/file/_equipment/';
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
                    EEquipSitePlannerImage::DRAW => array(
                        'value' => EEquipSitePlannerImage::DRAW,
                        'label' => '(Zeichnung)',
                        'attributes' => array(
                            'data-toggle' => 'details',
                        ),
                    ),
                    EEquipSitePlannerImage::IMAGE_1 => 'Bild 1',
                    EEquipSitePlannerImage::IMAGE_2 => 'Bild 2',
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
                    EEquipSitePlannerImage::ROUND_SHAPE     => 'Rund',
                    EEquipSitePlannerImage::RECTANGLE_SHAPE => 'Eckig'
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

    protected function prepareDataForSetData ($data)
    {
        // no pic uploaded
        if ($data['image1']['error'] > 0) unset($data['image1']);
        if ($data['image2']['error'] > 0) unset($data['image2']);

        if ($data['sitePlannerObject'] == '1') {
            if ($data['sitePlannerImage'] == NULL)
                $data['sitePlannerImage'] = 0;
            if (!isset($data['image']))
//                bdump('sdf');
            $data['image'] = ($data['sitePlannerImage'] == "0")
                ? EEquipSitePlannerImage::IMAGE_TYPE[$data->sitePlannerImage]
                : self::EQUIPMENT_IMAGES_PATH . $data['id'] . "/" . EEquipSitePlannerImage::IMAGE_TYPE[$data['sitePlannerImage']];
        }

        if ($data['sitePlannerImage'] == "0"){
            $data['length'] = ($data['length'] == "0" || $data['length'] == NULL) ? 100 : $data['length'];
            $data['width'] = ($data['width'] == "0" || $data['width'] == NULL) ? 100 : $data['width'];
        }
        return $data;
    }

    private function getUsersForSelect()
    {
        $list = $this->userService->getUserIdUserNameList();
        $list[0] = 'Verein';
        return $list;
    }
}