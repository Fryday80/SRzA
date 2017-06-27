<?php
namespace Equipment\Controller;

use Equipment\Service\TentService;
use Zend\Mvc\Controller\AbstractActionController;

class SitePlannerController extends AbstractActionController
{
    /** @var TentService  */
    private $tentService;

    public function __construct(TentService $tentService) {
        $this->tentService = $tentService;
    }

    public function indexAction() {
        $this->layout()->setVariable('showSidebar', false);
        return array(
            'tents' => $this->tentService->getCanvasData(),
        );
    }

}