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
    /** @var BlazonService $blaService */
    private $blaService;

    public function __construct(BlazonService $blazonService) {
        $this->blaService = $blazonService;
    }

    public function indexAction() {
        $blaTable = new BlazonDataTable( );
        $blaTable->setData($this->blaService->getAll());
        $blaTable->setButtons('all');
        $blaTable->insertLinkButton('/castmanager/wappen/add', 'Neues Wappen');
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

            if (!$this->blaService->exists($post['name'])) {
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
                    if ($data['blazon']['error'] == 0) {
                        $this->blaService->addNew($data['name'], $blaPath, $blaBigPath);
                        return $this->redirect()->toRoute('castmanager/wappen');
                    } else {
                        //todo error handling
                        bdump('file error');
                    }
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

        if (!$blazon = $this->blaService->getById($id)) {
            return $this->redirect()->toRoute('castmanager/wappen');
        }

        $form = new BlazonForm();
        $operator = 'Edit';
        if (isset($blazon['filename'])) {
            $form->get('blazon')->setAttribute('value', $blazon['filename']);
        }
        if (isset($blazon['filenameBig'])) {
            $form->get('blazonBig')->setAttribute('value', $blazon['filenameBig']);
        }
        $form->get('submit')->setAttribute('value', $operator);
        $form->populateValues($blazon);
        $form->setAttribute('action', '/castmanager/wappen/edit/' . $id);

        if ($request->isPost()) {
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $form->setData($post);
            $form->removeInputFilter(); //needed for edit
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
                if (!$item = $this->blaService->save($id, $data['name'], $blaPath, $blaBigPath)) {
                    //@todo errors to form
                } else {
                    // on success
                    //@todo cleanfix necessary ??
                    $form->addInputFilter();
                    return $this->redirect()->toRoute('castmanager/wappen');
                }
            }
        }
        bdump($form);
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
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->blaService->remove($id);
            }

            // Redirect to list of Users
            return $this->redirect()->toRoute('castmanager/wappen');
        }

        return array(
            'id' => $id,
            'blazon' => $this->blaService->getById($id)
        );
    }
}
