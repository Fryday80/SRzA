<?php
namespace Cast\Controller;

use Cast\Form\BlazonForm;
use Cast\Form\FamilyForm;
use Cast\Service\BlazonService;
use Cast\Utility\BlazonDataTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class BlazonController extends AbstractActionController
{
    public function indexAction() {
        /** @var BlazonService $blaService */
        $blaService = $this->getServiceLocator()->get("BlazonService");
        $blaTable = new BlazonDataTable( );
        bdump($blaService->getAll());
        $blaTable->setData($blaService->getAll());
        $blaTable->setButtons('all');
        $blaTable->insertLinkButton('/castmanager/wappen/add', 'add new familiy');
        $blaTable->insertLinkButton('/castmanager', 'Zurück');
        return new ViewModel(array(
            'blazons' => $blaTable,
        ));
    }
    public function addAction() {
        $form = new BlazonForm();
        $form->get('submit')->setValue('add');
        $form->setAttribute('action', '/castmanager/wappen/add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            //merge post data and files
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            /** @var BlazonService $blaService */
            $blaService = $this->getServiceLocator()->get("BlazonService");

            if (!$blaService->exists($post['name'])) {
                $form->setData($post);
                if ($form->isValid()) {
                    $data = $form->getData();
                    if ($data['blazon']['error'] == 0) {
                        $blaService->addNew($data['name'], (bool)$data['isOverlay'], $data['blazon']['tmp_name']);
                    } else {
                        //todo error handling
                        bdump('file error');
                    }
                return $this->redirect()->toRoute('castmanager/wappen');
                }
            } else {
                //@todo add error msg to form/name  "name already taken"
                bdump('wappen error "name already taken"');
            }

        }
        return array(
            'form' => $form
        );
    }
    public function editAction() {
        $request = $this->getRequest();
        $id = (int) $this->params()->fromRoute('id', null);
        if (! $id && !$request->isPost()) {
            return $this->redirect()->toRoute('castmanager/wappen');
        }

        /** @var BlazonService $blaService */
        $blaService = $this->getServiceLocator()->get("BlazonService");
        if (!$blazon = $blaService->getById($id)) {
            return $this->redirect()->toRoute('castmanager/wappen');
        }

        $form = new BlazonForm();
        $operator = 'Edit';
        $form->get('submit')->setAttribute('value', $operator);
        $form->populateValues($blazon);
        $form->setAttribute('action', '/castmanager/wappen/edit/' . $id);

        if ($request->isPost()) {
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $form->setData($post);
            if ($form->isValid()) {
                $data = $form->getData();
                $blaPath = $data['blazon']['tmp_name'];
                $blaBigPath = $data['blazonBig']['tmp_name'];
                if ($data['blazon']['error'] > 0) {
                    $blaPath = null;
                }
                if ($data['blazonBig']['error'] > 0) {
                    $blaBigPath = null;
                }
                if (!$item = $blaService->save($id, $data['name'], (bool)$data['isOverlay'], $blaPath, $blaBigPath)) {
                    //@todo errors to form
                } else {
                    return $this->redirect()->toRoute('castmanager/wappen');
                }
            }
        }
        return array(
            'id' => $id,
            'form' => $form,
            'blazon' => $blazon
        );
    }
    public function deleteAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (! $id) {
            return $this->redirect()->toRoute('castmanager/wappen');
        }
        /** @var BlazonService $blaService */
        $blaService = $this->getServiceLocator()->get("BlazonService");
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $blaService->remove($id);
            }

            // Redirect to list of Users
            return $this->redirect()->toRoute('castmanager/wappen');
        }

        return array(
            'id' => $id,
            'blazon' => $blaService->getById($id)
        );
    }
}
