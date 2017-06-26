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
        $a = $this->tentService->getAllTents();
        return array(
            'tents' => $this->tentService->getAllTents(),
        );
    }

}