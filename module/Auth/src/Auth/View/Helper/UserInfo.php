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
        if ($role == 'Guest') {
            return 'Wilkommem, Gast.';
        }
        return $role;
    }
}
