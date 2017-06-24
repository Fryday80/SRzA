<?php
namespace Auth\View\Helper;

use Application\Service\StatisticService;
use Application\Utility\URLModifier;
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
    /** @var StatisticService  */
    private $statsService;

    public function __construct(AuthStorage $storage, StatisticService $statisticService) {
        $this->storage = $storage;
        $this->statsService = $statisticService;
        return $this;
    }
    public function __invoke()
    {
        $role = $this->storage->getRoleName();
        $name = $this->storage->getUserName();
        $viewModel = new ViewModel();
        if (strtolower($role) == 'guest') {
            $viewModel->setTemplate("auth/auth/login.phtml");
            $viewModel->setVariable('form', new LoginForm('Login'));
            return $this->getView()->render($viewModel);
        } else {
            $logOutList = array(
                0 => array(
                    'name'  => 'Benutzer Daten',
                    'class' => 'myMenu',
                    'list'  => array(
                        0 => array(
                            'name' => 'Mein Profil Bearbeiten',
                            'url'  => '/profile'
                        ),
                        1 => array(
                            'name' => 'Meine Charaktere Bearbeiten',
                            'url'  => '/profile#characters'
                        ),
                    ),
                ),
                1 => $this->createActiveUsers($this->statsService->getActiveUsers()),
            );
            
            $viewModel->setTemplate("auth/auth/logout.phtml");
            $viewModel->setVariable('logOutList', $logOutList);
            return $this->getView()->render($viewModel);
        }

    }

    public function createActiveUsers($activeUsers)
    {
        $guestCount = 0;
        $actives = array ();
        $url = new URLModifier();
        $role = $this->storage->getRoleName();

        foreach ($activeUsers as $activeUser) {
            if ($activeUser->userName == 'Guest')
                $guestCount++;
            else {
                $userUrl = $url->toURL($activeUser->userName);
                $activeImg = '<img alt="active" src="/img/uikit/led-on.png" style="float: right; height: 15px;">';
                $actives[] = array(
                    'name' => $activeUser->userName . $activeImg,
                    'url' => "/profile/$userUrl"
                );
            }
        }
        if ($role == 'Administrator') {
            $call = ($guestCount == 1) ? ' Gast' : ' Gäste';
            $actives[] = array(
                'name' => $guestCount . $call . ' online',
                'url' => '#'
            );
        }

        return array(
            'name' => 'Aktive Benutzer',
            'class' => 'myMenu',
            'list' => $actives,
        );
    }
}
