<?php
namespace Auth\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Auth\Model\AuthStorage;

class UserInfo extends AbstractHelper
{
    /**
     *
     * @var AuthStorage
     */
    protected $storage;

    public function __construct(AuthStorage $storage) {
        $this->storage = $storage;
        return $this;
    }
    public function __invoke()
    {
        $role = $this->storage->getRoleName();
        $name = $this->storage->getUserName();
        $br = '<br>';
        $special_br = '<br class="M_S_br">';

        if ($role == 'Guest') {
            return 'Hallo '.$special_br.' Gast'.$br;
        } else {
            $showrole = '';
            if ($role = 'Administrator') {
                $showrole = $br.$role;
            }
            $expression = '<span class="greets">Hallo '.$special_br.$name.$showrole. '</span>';
            return $expression;
        }
        return $role;
    }
}
