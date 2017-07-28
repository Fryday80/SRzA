<?php
namespace Equipment\Controller;

use Application\Utility\DataTable;
use Equipment\Model\Tables\SitePlannerTable;
use Equipment\Service\EquipmentService;
use Exception;
use Media\Service\MediaException;
use Media\Service\MediaService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class SitePlannerController extends AbstractActionController
{
    /** @var EquipmentService  */
    private $equipmentService;
    /** @var $sitePlanTable SitePlannerTable  */
    private $sitePlanTable;
    /** @var MediaService  */
    private $mediaService;

    public function __construct(EquipmentService $equipmentService, SitePlannerTable $sitePlannerTable, MediaService $mediaService) {
        $this->equipmentService = $equipmentService;
        $this->sitePlanTable = $sitePlannerTable;
        $this->mediaService = $mediaService;
    }

    public function indexAction() {
        $data = $this->equipmentService->getAll();
        $this->layout()->setVariable('showSidebar', false);
        return array(
            'items' => $data,
        );
    }
    public function listAction() {
        $request = $this->getRequest();
//        $data = $this->sitePlanTable->getAllPlannerObjects();
        $data = $this->sitePlanTable->getAll();
        if ($request->isXmlHttpRequest()) {
            $result = ['error' => false];
            $result['data'] = $data;
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
//                echo json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_NUMERIC_CHECK  );
//                die;
            }
        }
        return new JsonModel($result);
    }

    public function saveAction() {
        $request = json_decode($this->getRequest()->getContent());
        $result = ['error' => false];

        if (!property_exists($request, 'name') ||
            !property_exists($request, 'data') ||
            !property_exists($request, 'longitude') ||
            !property_exists($request, 'latitude') ||
            !property_exists($request, 'diameter') ||
            !property_exists($request, 'mapType') ||
            !property_exists($request, 'scale') ||
            !property_exists($request, 'zoom')
        )
        {
            $this->response->setStatusCode(400);
            $result['error'] = true;
            $result['msg'] = "name, data, longitude, latitude, diameter, mapType, scale and zoom must be set";
        } else {
            try {
                $sitePlan = $this->sitePlanTable->hydrate(get_object_vars($request));
                $id = $this->sitePlanTable->save($sitePlan);
                if (is_numeric($id)) {
                    $result['msg'] = "Successfully inserted";
                    $result['newID'] = $id;
                } else {
                    $result['msg'] = "Successfully saved";
                    $result['newID'] = -1;
                }
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
    public function imageUploadAction() {
        $result = ['error' => false];
        $file = $this->params()->fromFiles('image');
        //@todo save path from config
        $savePath = 'Lageplaene';
        if (!$this->mediaService->isDir($savePath)) {
            $result['error'] = true;
            $result['msg'] = "save path doesn't exist";
            $result['code'] = 2;
        }
        if (!$file) {
            $result['error'] = true;
            $result['msg'] = "image must be set in the request";
            $result['code'] = 4;
        }
        if (!$result['error']) {
            $err = $this->mediaService->upload($file, $savePath);
            if ($err instanceof MediaException) {
                $result['error'] = true;
                $result['msg'] = $err->getMsg();
                $result['code'] = 5;
            } else {
                $result['msg'] = sprintf("image saved to '%s/%s'", $savePath, $file["name"]);
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
}