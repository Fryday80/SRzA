<?php
namespace Cast\Controller;

use Cast\Form\BlazonForm;
use Cast\Form\FamilyForm;
use Cast\Service\BlazonService;
use Cast\Utility\BlazonDataTable;
use Media\Service\ImageProcessor;
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

    public function addAction()
	{
		$this->blaService->setImageUploadPlugin($this->image());
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

            $form->setData($post);
            if ($form->isValid()) {
                $data = $form->getData();
                if ($data['blazon']['error'] == 0) {
                    $this->blaService->addNew($data['name'], $data['isOverlay'], $data['blazon'], $data['blazonBig']);
                    return $this->redirect()->toRoute('castmanager/wappen');
                } else {
                    //todo error handling
                    bdump('file error');
                }
            }

        }
        return array(
            'form' => $form
        );
    }
    public function editAction()
	{
		$this->blaService->setImageUploadPlugin($this->image());
        $request = $this->getRequest();
        $id = (int) $this->params()->fromRoute('id', null);
        if (! $id && !$request->isPost()) {
            return $this->redirect()->toRoute('castmanager/wappen');
        }
        if (!$blazon = $this->blaService->getById($id)) {
            return $this->redirect()->toRoute('castmanager/wappen');
        }

        $operator = 'Edit';
        $form = new BlazonForm($operator);
        if (isset($blazon['filename'])) {
            $form->get('blazon')->setAttribute('value', $blazon['filename']);
        }
        if (isset($blazon['filenameBig'])) {
            $form->get('blazonBig')->setAttribute('value', $blazon['filenameBig']);
        }
        $form->get('submit')->setAttribute('value', $operator);
        $form->populateValues($blazon->toArray());
        $form->setAttribute('action', '/castmanager/wappen/edit/' . $id);

        if ($request->isPost()) {
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $form->setData($post);
            if ($form->isValid()) {
                $data = $form->getData();
                if (!$item = $this->blaService->save($id, $data['isOverlay'], $data['name'], $data['blazon'], $data['blazonBig']))
                {
                    //@todo errors to form
                } else {
                    // on success
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
		$this->blaService->setImageUploadPlugin($this->image());
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
