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

        if ($role == 'Guest') {
            return 'Hallo Gast';
        } else {
            if ($role == 'Member') $roleName = 'Mitglied';
            elseif ($role == 'Vorstand') $roleName = $role;
            elseif ($role == 'Administrator') $roleName = $role;
            else $roleName = 'erweiterter Vorstand';
            $showrole = ' <span class="js-S-view">|</span> '.$roleName;
            $expression = '<span class="greets"> Hallo '.$name.'<br class="js-L-view">'.$showrole. '</span>';
            return $expression;
        }
        return $role;
    }
}
