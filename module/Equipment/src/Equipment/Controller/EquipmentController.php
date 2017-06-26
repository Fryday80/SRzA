<?php
namespace Equipment\Controller;

use Auth\Service\UserService;
use Equipment\Service\TentService;
use Equipment\Form\TentForm;
use Zend\Mvc\Controller\AbstractActionController;

class EquipmentController extends AbstractActionController
{

    /** @var TentService  */
    private $tentService;
    /** @var UserService  */
    private $userService;

    public function __construct(TentService $tentService, UserService $userService) {
        $this->tentService = $tentService;
        $this->userService = $userService;
    }

    public function indexAction() {
        return array(
            'form' => new TentForm($this->tentService, $this->userService),
        );
    }

}
