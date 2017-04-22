<?php
namespace Auth\Form;

use Zend\Form\Form;

class ProfileCharacterForm extends Form
{
    public $userList = array();
    public $familyList = array();
    public $jobs = array();

    public function __construct(Array $users, Array $families, Array $jobs)
    {
        $this->userList = $users;
        $this->familyList = $families;
        $this->jobs = $jobs;

        parent::__construct("Character");
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'name',
            'type' => 'text',
            'options' => array(
                'label' => 'Vorname'
            )
        ));
        $this->add(array(
            'name' => 'surename',
            'type' => 'text',
            'options' => array(
                'label' => 'Name'
            )
        ));
        $this->add(array(
            'name' => 'gender',
            'type' => 'Zend\Form\Element\Radio',
            'attributes' => array(),
            'options' => array(
                'label' => 'gender',
                'value_options' => array(
                    'm' => 'Mann',
                    'f' => 'Frau',
                ),
            ),
            'required' => true,
            'allow_empty' => false,

        ));
        $this->add(array(
            'name' => 'birthday',
            'type' => 'Zend\Form\Element\Date',
            'attributes' => array(),
            'options' => array(
                'label' => 'Birthday',
            ),
            'required' => true,
            'allow_empty' => false,

        ));
        $this->add(array(
            'name' => 'vita',
            'type' => 'textarea',
            'options' => array(
                'label' => 'Vita'
            ),
            'required' => true,
            'allow_empty' => false,
            'filters' => array(
                ['name' => 'StringTrim'],
            ),
        ));
        $this->add(array(
            'name' => 'family_id',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
            ),
            'options' => array(
                'label' => 'Familie',
                'value_options' => $this->getFamiliesForSelect(),
            )
        ));
        $this->add(array(
            'name' => 'guardian_id',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
            ),
            'options' => array(
                'disable_inarray_validator' => true,
                'label' => 'Vormund',
                'value_options' => $this->getGuardianForSelect(),
            )
        ));
        $this->add(array(
            'name' => 'tross_id',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
            ),
            'options' => array(
                'label' => 'Tross',
                'value_options' => $this->getFamiliesForSelect(),
            )
        ));
        $this->add(array(
            'name' => 'supervisor_id',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
            ),
            'options' => array(
                'disable_inarray_validator' => true,
                'label' => 'Vorgesetzter',
                'value_options' => $this->getSupervisorForSelect(),
            )
        ));
        $this->add(array(
            'name' => 'job_id',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
            ),
            'options' => array(
                'label' => 'Job',
                'value_options' => $this->getJobsForSelect(),
            )
        ));
        $this->add(array(
            'name' => 'save',
            'type' => 'Submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Speichern',
            )
        ));
    }
    public function getUsersForSelect()
    {
        $selectData = array();
        $selectData[0] = 'keiner';
        foreach ($this->userList as $user) {
            $selectData[$user['id']] = $user['name'];
        }
        return $selectData;
    }
    public function getFamiliesForSelect()
    {
        $selectData = array();
        $selectData[0] = 'keiner';
        foreach ($this->familyList as $fam) {
            $selectData[$fam['id']] = $fam['name'];
        }
        return $selectData;
    }

    private function getJobsForSelect() {
        $selectData = array();
        $selectData[0] = 'keiner';
        foreach ($this->jobs as $job) {
            $selectData[$job['id']] = $job['job'];
        }
        return $selectData;
    }

    private function getGuardianForSelect() {
        //wird im mom über js geladen
        return [0 => 'keiner'];
    }

    private function getSupervisorForSelect() {
        //wird im mom über js geladen
        return [0 => 'keiner'];
    }

}