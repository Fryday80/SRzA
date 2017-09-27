<?php
namespace Cast\Controller;

use Cast\Form\FamilyForm;
use Cast\Model\Tables\CharacterTable;
use Cast\Model\Tables\FamiliesTable;
use Cast\Model\Tables\JobTable;
use Zend\Mvc\Controller\AbstractActionController;

class ManagerController extends AbstractActionController
{
    /** @var CharacterTable $characterTable */
    private $characterTable;
    /** @var JobTable $jobTable */
    private $jobTable;
    /** @var FamiliesTable $familiesTable */
    private $familiesTable;

    public function __construct(CharacterTable $characterTable,
                                JobTable $jobTable,
                                FamiliesTable $familiesTable)
    {
        $this->characterTable = $characterTable;
        $this->jobTable = $jobTable;
        $this->familiesTable = $familiesTable;
    }

    public function indexAction() {
        //fine presentation of the cast
        $families = $this->familiesTable->getAll();
        $jobs = $this->jobTable->getAll();
        $characters = $this->characterTable->getAll();
        $return['items'] = array ('families', 'jobs', 'characters');
        foreach ($return['items'] as $item){
            $return[$item.'Count'] = count($$item);
        }
        return $return;
    }
    public function addAction() {
        $form = new FamilyForm();
        $form->get('submit')->setValue('add');
        $form->setAttribute('action', '/families/add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $this->familiesTable->add($data);
                return $this->redirect()->toRoute('families');
            }
        }
        return array(
            'form' => $form
        );
    }
    public function editAction(){
        $request = $this->getRequest();
        $id = (int) $this->params()->fromRoute('id', null);
        if (! $id && !$request->isPost()) {
            return $this->redirect()->toRoute('/families');
        }
        if (!$family = $this->familiesTable->getById($id)) {
            return $this->redirect()->toRoute('/families');
        }
        $form = new FamilyForm();
        $operator = 'Edit';
        $form->get('submit')->setAttribute('value', $operator);
        $form->populateValues($family);
        $form->setAttribute('action', '/families/edit/' . $id);

        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->familiesTable->save($id, $form->getData());
                return $this->redirect()->toRoute('families');
            }
        }
        return array(
            'id' => $id,
            'form' => $form
        );
    }
    public function deleteAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (! $id) {
            return $this->redirect()->toRoute('families');
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->familiesTable->remove($id);
            }

            // Redirect to list of Users
            return $this->redirect()->toRoute('families');
        }

        return array(
            'id' => $id,
            'family' => $this->familiesTable->getById($id)
        );
    }
}
