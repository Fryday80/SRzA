<?php
	use Equipment\Model\Enums\EEquipLending;
	use Equipment\Model\Enums\EStoragePlaces;

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
//		}
//	}