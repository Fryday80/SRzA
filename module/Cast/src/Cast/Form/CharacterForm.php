<?php
namespace Cast\Form;

use Zend\Form\Form;

class CharacterForm extends Form
{
    public $userList = array();
    public $familyList = array();
    public $jobs = array();
    public $guardians = array();
    public $supervisors = array();

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
            'name' => 'user_id',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
            ),
            'options' => array(
                'label' => 'Darsteller',
                'value_options' => $this->getUsersForSelect(),
            )
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
            'name' => 'active',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'Aktiv',
            ),
        ),
        array(
            'priority' => 11, // Increase value to move to top of form
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Submit',
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
    public function setPossibleGuardians($guardians) {
        $this->guardians = $guardians;
        $this->get('guardian_id')->setValueOptions($this->getGuardianForSelect());
    }
    public function setPossibleSupervisors($supervisors) {
        $this->supervisors = $supervisors;
        $this->get('supervisor_id')->setValueOptions($this->getSupervisorForSelect());
    }
    private function getGuardianForSelect() {
        $selectData = array();
        $selectData[0] = 'keiner';
        foreach ($this->guardians as $guardian) {
            $selectData[$guardian['id']] = $guardian['name'];
        }
        return $selectData;
    }

    private function getSupervisorForSelect() {
        $selectData = array();
        $selectData[0] = 'keiner';
        foreach ($this->supervisors as $supervisor) {
            $selectData[$supervisor['id']] = $supervisor['name'];
        }
        return $selectData;
    }

}