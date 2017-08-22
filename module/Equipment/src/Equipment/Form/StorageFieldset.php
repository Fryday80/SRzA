<?php
	/**
	 * Created by IntelliJ IDEA.
	 * User: Fry
	 * Date: 22.08.2017
	 * Time: 12:08
	 */

	namespace Equipment\Form;


	use Equipment\Model\Enums\EEquipLending;
	use Equipment\Model\Enums\EStoragePlaces;
	use Zend\Form\Fieldset;

	class StorageFieldset extends Fieldset
	{
		public function __construct($name = null, $options = array())
		{
			parent::__construct($name, $options);

			$this->add(array(
				'name' => 'purchased',
				'type' => 'Text',
				'options' => array(
					'label' => 'Kaufdatum',
				),
			));

			$this->add(array(
				'name' => 'amount',
				'type' => 'Text',
				'options' => array(
					'label' => 'Neupreis',
				),
			));

			$this->add(array(
				'name' => 'lending',
				'type' => 'Radio',
				'options' => array(
					'label' => 'Darf verliehen werden',
					'value_options' => EEquipLending::LEND_LIST,
				),
				'attributes' => array(
					'value' => 0,
				),
			));

			$this->add(array(
				'name' => 'stored',
				'type' => 'Radio',
				'options' => array(
					'label' => 'Lagerplatz',
					'value_options' => EStoragePlaces::LIST,
				),
				'attributes' => array(
					'value' => 0,
				),
			));

			$this->add(array(
				'name' => 'bill',
				'type' => 'File',
				'options' => array(
					'label' => 'Rechnungskopie',
				),
			));
		}
	}