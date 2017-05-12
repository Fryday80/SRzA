<?php
namespace Auth\View\Helper;

use Auth\Service\LogoutDataService;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Auth\Model\AuthStorage;
use Auth\Form\LoginForm;

class LoginView extends AbstractHelper
{
    /**
     *
     * @var AuthStorage
     */
    protected $storage;
    protected $dataService;

    public function __construct(AuthStorage $storage, LogoutDataService $dataService) {
        $this->storage = $storage;
        $this->dataService = $dataService;
        return $this;
    }
    public function __invoke()
    {
        $role = $this->storage->getRoleName();
        $name = $this->storage->getUserName();
        $viewModel = new ViewModel();
        if ($role == 'guest' || $role == 'Guest') {
            $viewModel->setTemplate("auth/auth/login.phtml");
            $viewModel->setVariable('form', new LoginForm('Login'));
            return $this->getView()->render($viewModel);
        } else {
            $viewModel->setTemplate("auth/auth/logout.phtml");
            $viewModel->setVariable('dataService', $this->dataService);
//            $viewModel->setVariable('user', $name);
//            $viewModel->setVariable('role', $role);
            return $this->getView()->render($viewModel);
        }

    }
}
