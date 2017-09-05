<?php
namespace Equipment\Form;

use Application\Form\MyForm;
use Auth\Service\UserService;
use Equipment\Model\Enums\EEquipSitePlannerImage;
use Equipment\Service\EquipmentService;

class EquipmentForm extends MyForm
{
    const EQUIPMENT_IMAGES_PATH = '/media/file/equipment/';
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
            'name' => 'userName',
            'type' => 'hidden'
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
        $this->add(array(
            'name' => 'sitePlannerImage',
            'type' => 'Zend\Form\Element\Radio',
            'required' => false,
            'attributes' => array(
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
                            'data-toggleGrp' => 'sitePlannerImage',
                        ),
                    ),
                    EEquipSitePlannerImage::IMAGE_1 => 'Bild 1',
                    EEquipSitePlannerImage::IMAGE_2 => 'Bild 2',
                ),
            ),
        ));
        $this->add(array(
            'name' => 'image',
            'type' => 'hidden',
        ));
        $this->add(array(
            'name' => 'color1',
            'type' => 'Zend\Form\Element\Color',
            'required' => true,
            'attributes' => array(
                'data-toggleGrp' => 'details,sitePlannerImage',
                'value' => '#FAEBd7',
            ),
            'options' => array(
                'label' => 'Farbe',
            ),
        ));
        $this->add(array(
            'name' => 'shape',
            'type' => 'select',
            'attributes' => array(
                'data-toggleGrp' => 'details,sitePlannerImage',
            ),
            'options' => array(
               'label' => 'Form bei Zeichnung',
                'value_options' => array(
                    EEquipSitePlannerImage::ROUND_SHAPE     => 'Rund',
                    EEquipSitePlannerImage::OVAL_SHAPE      => 'Oval',
                    EEquipSitePlannerImage::RECTANGLE_SHAPE => 'Eckig'
                ),
            ),
        ));

        include ('StorageFieldset.php');

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

	protected function prepareDataForSetData ($data)
	{
		if (!isset($data['lending']) || $data['lending'] == null) $data['lending'] = 0;
		if (!isset($data['stored']) || $data['stored'] == null) $data['stored'] = 0;
		$isEdit = false;
		$newImage = false;
		$images = array ('image1', 'image2');
		foreach ($images as $key => $image) {
			if (isset($data[$image])){
				// source is sent form
				if (is_array($data[$image]))
				{
					$isEdit = true;
					// was a image uploaded?
					if (isset($data[$image]['error']) && $data[$image]['error'] > 0) {
						unset ($data[ $image ]);
						unset ($images[$key]);
					}
					else $newImage = true;
				}
			}
		}

		if ($isEdit)
		{
			if ($data['sitePlannerObject'] == '0' || $data['sitePlannerObject'] == null) unset ($data['image']);
			else {
				if ($data['sitePlannerImage'] == NULL || $data['sitePlannerImage'] == "0")
				{
					unset ($data['image']);
					$data['depth'] = ($data['depth'] == "0" || $data['depth'] == NULL) ? 100 : $data['depth'];
					$data['width'] = ($data['width'] == "0" || $data['width'] == NULL) ? 100 : $data['width'];
					if ($data['shape'] == EEquipSitePlannerImage::ROUND_SHAPE)
						$data['width'] = $data['depth'];
				}
				else
				{
					// if selected image was uploaded
					if ($newImage && isset($data[ EEquipSitePlannerImage::IMAGE_TYPE[ $data['sitePlannerImage'] ] ]) && is_array($data[ EEquipSitePlannerImage::IMAGE_TYPE[ $data['sitePlannerImage'] ] ])) {
						$imageData = $data[ EEquipSitePlannerImage::IMAGE_TYPE[ $data['sitePlannerImage'] ] ];
						$parts = explode('.', $imageData['name']);
						$ext = '.' . $parts[ (count($parts) - 1) ];
						$imageName = EEquipSitePlannerImage::IMAGE_TYPE[ $data['sitePlannerImage'] ] . $ext;
						$data['image'] = self::EQUIPMENT_IMAGES_PATH . $data['id'] . "/" . $imageName;
					}
					// if the selected image was not uploaded, handle selection change
					else {
						if (!isset($data['image' . $data['sitePlannerImage']])) {
							$dbItem = $this->equipService->getById($data['id']);
							$data['image'] = $dbItem['image' . $data['sitePlannerImage']];
						}
					}
				}
			}
		}

		return $data;
	}
}