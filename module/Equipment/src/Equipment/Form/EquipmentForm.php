<?php
namespace Equipment\Form;

use Application\Form\SRAForm;
use Auth\Service\UserService;
use Equipment\Model\Enums\EEquipSitePlannerImage;
use Equipment\Service\EquipmentService;

class EquipmentForm extends SRAForm
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
		// cleanfix normalize old entries - earlier than 05.09.2017 - can be removed for launch
		if (!isset($data['lending']) || $data['lending'] == null) $data['lending'] = 0;
		if (!isset($data['stored']) || $data['stored'] == null) $data['stored'] = 0;

		// vars
		$isEdit = false;
		$newImage = false;
		$images = array ('image1', 'image2');

		// prepare
		foreach ($images as $fieldName) //
		{
			if (isset($data[$fieldName])){
				// if source is form posted form && upload array
				if (is_array($data[$fieldName]))
				{
					$isEdit = true; // set when post data from form
					// was a image uploaded?
					// --no
					if (isset($data[$fieldName]['error']) && $data[$fieldName]['error'] > 0) // check for concrete upload array key ['error'] && if error code != 0
					{
						// delete if error occurred
						unset ($data[ $fieldName ]); 	// from data set
						unset ($images[$fieldName]);			// delete from $images
					}
					// --yes
					else $newImage = true;
				}
			}
		}

		// if data set is from form
		if ($isEdit)
		{
			// is planner object
			// --no
			if ($data['sitePlannerObject'] == '0' || $data['sitePlannerObject'] == null) $data['image'] = null;
			// --yes
			else {
				// check selection of witch image should be used
				// -- if not set (null) or "0" (drawing)
				if ($data['sitePlannerImage'] == NULL || $data['sitePlannerImage'] == "0")
				{
					$data['image'] = null;
					// add default values if nothing was set to avoid errors in SitePlanner
					$data['depth'] = ($data['depth'] == "0" || $data['depth'] == NULL) ? 100 : $data['depth'];
					$data['width'] = ($data['width'] == "0" || $data['width'] == NULL) ? 100 : $data['width'];
					// set diameter on 'width' if round shape was selected
					if ($data['shape'] == EEquipSitePlannerImage::ROUND_SHAPE)
						$data['width'] = $data['depth'];
				}
				// -- if an image was selected
				else
				{
					$data['image'] = ($data['sitePlannerImage'] == EEquipSitePlannerImage::IMAGE_2) ? $data['image2'] : $data['image1'];
				}
			}
		}

		return $data;
	}
}