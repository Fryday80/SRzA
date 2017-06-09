<?php
namespace Cast\Controller;

use Cast\Service\BlazonService;
use Cast\Service\CastService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CastController extends AbstractActionController
{
    /** @var CastService $castService */
    private $castService;
    /** @var BlazonService  */
    private $blaService;

    public function __construct(CastService $castService) {
        $this->castService = $castService;
    }

    public function indexAction() {
        $this->layout()->setVariable('showSidebar', false);
        return new ViewModel(array(
            'root' => $this->castService->getStanding()
        ));
    }

    private function createReference($char){
        $parentBlazons[$char['id']] = $char['blazon_id'];
        if (isset($char['employ'])){
            foreach ($char['employ'] as $employee){
                $this->createReference($employee);
            }
        }
        return $parentBlazons;
    }
}
