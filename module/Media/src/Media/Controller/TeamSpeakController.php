<?php
namespace Media\Controller;


use Media\Service\MediaService;
use Media\Service\TeamSpeakService;
use Media\Utility\ts3admin;
use Zend\Mvc\Controller\AbstractActionController;

class TeamSpeakController extends AbstractActionController  {

    /** @var  TeamSpeakService */
    private $tsService;

    public function __construct(TeamSpeakService $tsService)
    {
        $this->tsService = $tsService;
    }
    public function indexAction()
    {

    }
}