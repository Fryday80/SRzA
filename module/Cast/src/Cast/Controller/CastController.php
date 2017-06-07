<?php
namespace Cast\Controller;

use Cast\Service\CastService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CastController extends AbstractActionController
{
    /** @var CastService $castService */
    private $castService;

    public function __construct(CastService $castService) {
        $this->castService = $castService;
    }

    public function indexAction() {
        $this->layout()->setVariable('showSidebar', false);
        /** @var CastService $castService */
        $this->castService->getStanding();
        return new ViewModel(array(
            'root' => $this->castService->getStanding(),
        ));
    }
}
