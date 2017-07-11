<?php
namespace Cast\Controller;

use Cast\Form\FamilyForm;
use Cast\Model\FamiliesTable;
use Cast\Service\BlazonService;
use Cast\Service\CastService;
use Cast\Utility\FamilyDataTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class FamilyController extends AbstractActionController
{
    /** @var  BlazonService */
    private $blazonService;
    /** @var CastService  */
    private $castService;

    public function __construct(CastService $castService, BlazonService $blazonService) {
        $this->castService = $castService;
        $this->blazonService = $blazonService;
    }

    public function indexAction() {
        $famTable = new FamilyDataTable( );
        $famTable->setData($this->castService->getAllFamilies());
        $famTable->setButtons('all');
        $famTable->insertLinkButton('/castmanager/families/add', 'add new familiy');
        $famTable->insertLinkButton('/castmanager', 'Zurück');
        return new ViewModel( array(
            'families' => $famTable,
        ) );
    }
    public function addAction() {
        $form = new FamilyForm($this->blazonService);
        $form->get('submit')->setValue('add');
        $form->setAttribute('action', '/castmanager/families/add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            //merge post data and files
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $form->setData($post);
            if ($form->isValid()) {
                $data = $form->getData();
                $this->castService->addFamily($data);
                return $this->redirect()->toRoute('castmanager/families');
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
            return $this->redirect()->toRoute('castmanager/families');
        }
        if (!$family = $this->castService->getFamilyById($id)) {
            return $this->redirect()->toRoute('castmanager/families');
        }
        $form = new FamilyForm($this->blazonService);
        $operator = 'Edit';
        $form->get('submit')->setAttribute('value', $operator);
        $form->populateValues($family);
        $form->setAttribute('action', '/castmanager/families/edit/' . $id);

        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->castService->saveFamily($id, $form->getData());
                return $this->redirect()->toRoute('castmanager/families');
            }
        }
        return array(
            'id' => $id,
            'form' => $form,
            'filePath' => '/media/file/wappen/',
            'data' => array(
                'family' => $family,
                'blazon' => $this->blazonService->getById( $family['blazon_id'] )
            ),
        );
    }
    public function deleteAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (! $id) {
            return $this->redirect()->toRoute('castmanager/families');
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->castService->removeFamily($id);
            }

            // Redirect to list of Users
            return $this->redirect()->toRoute('castmanager/families');
        }

        return array(
            'id' => $id,
            'family' => $this->castService->getFamilyById($id)
        );
    }
}
