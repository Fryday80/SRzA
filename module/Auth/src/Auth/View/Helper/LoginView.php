<?php
namespace Auth\View\Helper;

use Auth\Service\MyMenuService;
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
    protected $myMenuService;

    public function __construct(AuthStorage $storage, MyMenuService $myMenuService) {
        $this->storage = $storage;
        $this->myMenuService = $myMenuService;
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
            $viewModel->setVariable('myMenuService', $this->myMenuService);
//            $viewModel->setVariable('user', $name);
//            $viewModel->setVariable('role', $role);
            return $this->getView()->render($viewModel);
        }

    }
}
