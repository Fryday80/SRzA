<?php
namespace Equipment\Controller;

use Application\Utility\DataTable;
use Auth\Service\AccessService;
use Auth\Service\UserService;
use Equipment\Form\TentColorsForm;
use Equipment\Form\TentTypeForm;
use Equipment\Model\Tent;
use Equipment\Service\TentService;
use Equipment\Form\TentForm;
use Zend\Mvc\Controller\AbstractActionController;

class EquipmentController extends AbstractActionController
{

    /** @var TentService  */
    private $tentService;
    /** @var UserService  */
    private $userService;
    /** @var AccessService  */
    private $accessService;

    public function __construct(TentService $tentService, UserService $userService, AccessService $accessService) {
        $this->tentService = $tentService;
        $this->userService = $userService;
        $this->accessService = $accessService;
    }

    public function indexAction() {
        $a = $this->tentService->getAllTents();

        $test = 'empty';
        $dataTable = 'empty';
        $test = json_encode($a);
        $hide = array();
        $dataTableData = $this->tentService->getAllTents()->toArray();

        $dataTable = new DataTable(array('data' => $dataTableData));
        $dataTable->remove('id');
        $form = new TentForm($this->tentService, $this->userService);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $form->setData($post);
            if ($form->isValid()){
                $test = $data = new Tent($form->getData());
                $this->tentService->saveTent($data);
            }
        }
        return array(
            'form' => $form,
            'test' => $test,
            'dataTable' => $dataTable
        );
    }

    public function addAction()
    {
        $form = new TentForm($this->tentService, $this->userService);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $form->setData($post);
            if ($form->isValid()){
                $data = new Tent($form->getData());
                $this->tentService->saveTent($data);
            }
        }

    }

}
