<?php
namespace Equipment\Controller;

use Equipment\Service\EquipmentService;
use Zend\Mvc\Controller\AbstractActionController;

class SitePlannerController extends AbstractActionController
{
    /** @var EquipmentService  */
    private $equipmentService;

    public function __construct(EquipmentService $equipmentService) {
        $this->equipmentService = $equipmentService;
    }

    public function indexAction() {
        $this->layout()->setVariable('showSidebar', false);
        return array(
            'tents' => $this->equipmentService->getCanvasData(),
        );
    }

}