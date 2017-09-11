<?php
	/**
	 * Created by IntelliJ IDEA.
	 * User: Fry
	 * Date: 11.09.2017
	 * Time: 13:13
	 */

	namespace Application\Form;


	class ContactForm extends SRAForm
	{
		public function __construct()
		{
			parent::__construct("Contact");
			$this->setAttribute('method', 'post');

			$this->add(array(
				'name' => 'name',
				'type' => 'text',
				'options' => array(
					'label' => 'Dein Name'
				)
			));
			$this->add(array(
				'name' => 'preName',
				'type' => 'text',
				'options' => array(
					'label' => 'Dein Vorname'
				)
			));
			$this->add(array(
				'name' => 'street',
				'type' => 'text',
				'options' => array(
					'label' => 'Strasse und Hausnummer'
				)
			));
			$this->add(array(
				'name' => 'zip',
				'type' => 'number',
				'options' => array(
					'label' => 'Postleitzahl'
				)
			));
			$this->add(array(
				'name' => 'city',
				'type' => 'text',
				'options' => array(
					'label' => 'Ort'
				)
			));

			$this->add(array(
				'name' => 'birthday',
				'type' => 'Zend\Form\Element\Date',
				'options' => array(
					'label' => 'Geburtsdatum',
				),
			));

			$this->add(array(
				'name' => 'email',
				'type' => 'email',
				'options' => array(
					'label' => 'Emailadresse'
				)
			));
			$this->add(array(
				'name' => 'phone',
				'type' => 'number',
				'options' => array(
					'label' => 'Telefonnummer'
				)
			));

			$this->add(array(
				'name' => 'appendix',
				'type' => 'textarea',
				'options' => array(
					'label' => 'Zusätzliche Angaben'
				)
			));
		}
	}