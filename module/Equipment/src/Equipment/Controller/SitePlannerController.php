<?php
namespace Equipment\Controller;

use Application\Utility\DataTable;
use Equipment\Model\SitePlannerTable;
use Equipment\Service\EquipmentService;
use Exception;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class SitePlannerController extends AbstractActionController
{
    /** @var EquipmentService  */
    private $equipmentService;
    /** @var $sitePlanTable SitePlannerTable  */
    private $sitePlanTable;

    public function __construct(EquipmentService $equipmentService, SitePlannerTable $sitePlannerTable) {
        $this->equipmentService = $equipmentService;
        $this->sitePlanTable = $sitePlannerTable;
    }

    public function indexAction() {
        $this->layout()->setVariable('showSidebar', false);
        return array(
            'tents' => $this->equipmentService->getCanvasData(),
        );
    }
    public function listAction() {
        $request = $this->getRequest();
        $data = $this->sitePlanTable->getAll();

        if ($request->isXmlHttpRequest()) {
            $result = ['error' => false];
            if (!$data) {
                $this->response->setStatusCode(500);
                $result['error'] = true;
                $result['msg'] = "internal server error";
            } else {
                $result['data'] = $data;
            }
            return new JsonModel($result);
        } else {
            $dataTable = new DataTable(array(
                'data' => $data,
                'columns' => array(
                    array (
                        'name' => 'id',
                        'label' => 'ID'
                    ),
                    array (
                        'name' => 'name',
                        'label' => 'Name'
                    ),
                )
            ));
            $dataTable->insertLinkButton('/siteplanner', 'Zum Planner');
            return array(
                'dataTable' => $dataTable
            );
        }
    }
    public function getAction() {
        $id = $this->params('id');
        $result = ['error' => false];
        if (!$id) {
            $this->response->setStatusCode(400);
            $result['error'] = true;
            $result['msg'] = "need param 'id'!";
        } else {
            //load data
            $data = $this->sitePlanTable->getById($id);
            if (!$data || count($data) == 0) {
                $this->response->setStatusCode(500);
                $result['error'] = true;
                $result['msg'] = "Site Plan with id $id not found";
            } else {
                $result['data'] = $data;
            }
        }
        return new JsonModel($result);
    }

    public function saveAction() {
        $request = json_decode($this->getRequest()->getContent());
        $result = ['error' => false];

        if (!property_exists($request, 'id') ||
            !property_exists($request, 'name') ||
            !property_exists($request, 'data') ||
            !property_exists($request, 'longitude') ||
            !property_exists($request, 'latitude'))
        {
            $this->response->setStatusCode(400);
            $result['error'] = true;
            $result['msg'] = "id, name, longitude, latitude and data must be set";
        } else {
            try {
                $sitePlan = $this->sitePlanTable->hydrate(get_object_vars($request));
                $this->sitePlanTable->save((int)$request->id, $sitePlan);
                $result['msg'] = "id, name, longitude, latitude and data must be set";
            } catch (Exception $e) {

                $result['code'] = 500;
                $result['msg'] = $e->getMessage();
                $result['file'] = $e->getFile();
                $result['line'] = $e->getLine();
                $result['stack'] = $e->getTrace();
            }

        }

        return new JsonModel($result);
    }

    public function deleteAction() {
        $id = $this->params('id');
        $result = ['error' => false];
        if (!$id) {
            $this->response->setStatusCode(400);
            $result['error'] = true;
            $result['msg'] = "need param 'id'!";
        } else {
            //load data
            $data = $this->sitePlanTable->getById($id);
            if (!$data || count($data) == 0) {
                $this->response->setStatusCode(500);
                $result['error'] = true;
                $result['msg'] = "Site Plan with id $id not found";
            } else {
                $result['data'] = $data;
            }
        }
        return new JsonModel($result);
    }

    public function jsonAction() {
        /** @var  $statsService StatisticService */
        $statsService = $this->statsService;
        $request = json_decode($this->getRequest()->getContent());
        $result = ['error' => false];
        if (!property_exists($request, 'method') ) {
            $this->response->setStatusCode(400);
            $result['error'] = true;
            $result['msg'] = "need param 'method'!";
            return new JsonModel($result);
        }
        switch ($request->method) {
            case 'getLiveActions' :
                if (property_exists($request, 'since') ) {
                    $result['actions'] = Microtime::addDateTime( $statsService->getActionLog($request->since) );
                } else {
                    $this->response->setStatusCode(400);
                    $result['error'] = true;
                    $result['msg'] = "need param 'since'!";
                }
                break;
            default:
                $this->response->setStatusCode(400);
                $result['error'] = true;
                $result['msg'] = "Method do not exist!";
        };
        //output
        return new JsonModel($result);
    }

}