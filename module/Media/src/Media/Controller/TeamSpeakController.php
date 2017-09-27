<?php
namespace Media\Controller;


use Auth\Service\AclService;
use Auth\Service\UserService;
use Media\Service\MediaService;
use Media\Service\TeamSpeakService;
use Media\Utility\ts3admin;
use Zend\Mvc\Controller\AbstractActionController;

class TeamSpeakController extends AbstractActionController  {

    /** @var  TeamSpeakService */
    private $tsService;
    /** @var UserService  */
    private $userService;
    private $acl;

    public function __construct(TeamSpeakService $tsService, UserService $userService, AclService $acl)
    {
        $this->tsService = $tsService;
        $this->userService = $userService;
        $this->acl = $acl;
    }
    public function indexAction()
    {
        return array(
            "tsService" => $this->tsService
        );
    }
}