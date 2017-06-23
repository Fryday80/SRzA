<?php
namespace Cast\Form;

use Cast\Form\Filter\CharacterFilter;
use Cast\Service\CastService;
use Zend\Form\Form;

class CharacterForm extends Form
{
    public $userList = array();
    public $familyList = array();
    public $jobs = array();
    public $guardians = array();
    public $supervisors = array();
    /** @var CastService  */
    private $castService;

    public function __construct( CastService $castService )
    {
        parent::__construct("Character");
        $this->castService = $castService;
        $this->userList    = $castService->getAllUsers()->toArray();
        $this->familyList  = $castService->getAllFamilies();
        $this->jobs = $castService->getAllJobs();

        $this->setAttribute('method', 'post');
        $this->setInputFilter(new CharacterFilter());
        // fields
        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));  // id       - hidden
        $this->add(array(
            'name' => 'user_id',
            'type' => 'hidden'
        ));  // user_id  - hidden
        $this->add(array(
            'name' => 'name',
            'type' => 'text',
            'options' => array(
                'label' => 'Vorname'
            ),
            'attributes' => array(
                'autofocus' => 'autofocus',
            ),
            'required' => true,
            'allow_empty' => false,
            'filters' => array(
                ['name' => 'StringTrim'],
            ),
        ));  // name     - text
        $this->add(array(
            'name' => 'surename',
            'type' => 'text',
            'options' => array(
                'label' => 'Name'
            ),
            'required' => true,
            'allow_empty' => false,
            'filters' => array(
                ['name' => 'StringTrim'],
            ),
        ));  // surename - text
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

        ));  // gender   - radio
        $this->add(array(
            'name' => 'birthday',
            'type' => 'Zend\Form\Element\Date',
            'attributes' => array(),
            'options' => array(
                'label' => 'Birthday',
            ),
            'required' => true,
            'allow_empty' => false,

        ));  // birthday - date
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
        ));  // vita     - textarea

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Speichern',
            ),
        ));  // submit
    }

    public function isBackend()
    {
        $this->setInputFilter(new CharacterFilter('backend'));
        $this->add(array(
            'name' => 'user_id',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
            ),
            'options' => array(
                'label' => 'Darsteller',
                'value_options' => $this->getUsersForSelect(),
            ),
            'required' => true,
            'allow_empty' => false,
            'filters' => array(
                ['name' => 'StringTrim'],
            ),
        ));  // user_id       - select
        $this->add(array(
            'name' => 'job_id',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
            ),
            'options' => array(
                'label' => 'Job',
                'value_options' => $this->getJobsForSelect(),
            )
        ));  // job_id        - select
        $this->add(array(
            'name' => 'tross_id',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
            ),
            'options' => array(
                'label' => 'Tross',
                'value_options' => $this->getFamiliesForSelect(),
            )
        ));  // tross_id      - select
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
        ));  // supervisor_id - select
        $this->add(array(
            'name' => 'family_id',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
            ),
            'options' => array(
                'label' => 'Family',
                'value_options' => $this->getFamiliesForSelect(),
            )
        ));  // family_id     - select
        $this->add(array(
            'name' => 'guardian_id',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
            ),
            'options' => array(
                'disable_inarray_validator' => true,
                'label' => 'Familien "Vorgesetzter"',
                'value_options' => $this->getGuardianForSelect(),
            )
        ));  // guardian_id   - select
        $this->add(array(
            'name' => 'active',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'Aktiv',
            ),
        ));  // active        - checkbox

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Speichern',
            ),
        ));  // submit
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
        $selectData[0] = 'selbst Oberhaupt';
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