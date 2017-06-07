<?php
namespace Cast\Controller;

use Cast\Form\JobForm;
use Cast\Model\JobTable;
use Cast\Utility\JobDataTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class JobController extends AbstractActionController
{
    /** @var JobTable */
    private $jobTable;

    public function __construct(JobTable $jobTable) {
        $this->jobTable = $jobTable;
    }

    public function indexAction() {
        $jobs = $this->jobTable->getAll();
        $jobsTable = new JobDataTable();
        $jobsTable->setData($jobs);
        $jobsTable->setButtons('all');
        $jobsTable->insertLinkButton('/castmanager/jobs/add', 'Add new job');
        $jobsTable->insertLinkButton('/castmanager', 'Zurück');

        return new ViewModel(array(
            'jobs' => $jobsTable,
        ));
    }
    public function addAction() {
        $form = new JobForm();
        $form->get('submit')->setValue('add');
        $form->setAttribute('action', '/castmanager/jobs/add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $this->jobTable->add($data);
                return $this->redirect()->toRoute('castmanager/jobs');
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
            return $this->redirect()->toRoute('castmanager/jobs');
        }
        if (!$family = $this->jobTable->getById($id)) {
            return $this->redirect()->toRoute('castmanager/jobs');
        }
        $form = new JobForm();
        $operator = 'Edit';
        $form->get('submit')->setAttribute('value', $operator);
        $form->populateValues($family);
        $form->setAttribute('action', '/castmanager/jobs/edit/' . $id);

        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->jobTable->save($id, $form->getData());
                return $this->redirect()->toRoute('castmanager/jobs');
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
            return $this->redirect()->toRoute('castmanager/jobs');
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->jobTable->remove($id);
            }

            // Redirect to list of Users
            return $this->redirect()->toRoute('castmanager/jobs');
        }

        return array(
            'id' => $id,
            'job' => $this->jobTable->getById($id)
        );
    }
}
