<?php
namespace Auth\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Auth\Model\AuthStorage;

class UserInfo extends AbstractHelper
{
	const ROLE = 0;
	const NAME = 1;
	const IS_LOGGED_IN = 2;
    /**
     *
     * @var AuthStorage
     */
    protected $storage;

    public function __construct(AuthStorage $storage) {
        $this->storage = $storage;
        return $this;
    }

    public function __invoke($info = null)
    {
        $role = $this->storage->getRoleName();
        $name = $this->storage->getUserName();
		if ($info == self::ROLE)  		 return $role;
		if ($info == self::NAME)   		 return $name;
		if ($info == self::IS_LOGGED_IN) return (!($role == 'Guest'));

        if ($role == 'Guest') {
            return 'Hallo Gast';
        } else {
            if ($role == 'Member') $roleName = 'Mitglied';
            elseif ($role == 'Vorstand') $roleName = $role;
            elseif ($role == 'Administrator') $roleName = $role;
            else $roleName = 'erweiterter Vorstand';
            $showRole = $roleName;
            $expression = '<span class="greets"> Hallo '.$name.'<br class="js-L-view">'.$showRole. '</span>';
            return $expression;
        }
    }
}
