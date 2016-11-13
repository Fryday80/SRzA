<?php
namespace MemberManager\Service;

Class MemberManagerService
{
    private $profileTable;
    private $presentationTable;
    private $authTable;
    private $cashTable;



    function __construct($sm)
    {
        $this->profileTable = $sm->get('MemberManager\Model\AlbumsTable');
        $this->presentationTable = $sm->get('MemberManager\Model\PresentationTable');
        $this->cashTable = $sm->get('MemberManager\Model\CashTable');
        $this->authTable = $sm->get('Auth\Model\AuthService???'); //fry Auth-daten salt
    }

    public function getAllUser() {
        return $this->profileTable->fetchAllUser();
    }

    public function getUserByID($user_id) {
        return $this->profileTable->getUserByID($user_id);
        
        
    }

    public function fetchWholeUserData($user_id) {
        $profile = $this->getUserByID($user_id); //salt
        $auth = $this->authTable->getByID($user_id);
        $presentation = $this->presentationTable->getByID($user_id);
        $cash = $this->cashTable->getByID($user_id);
        return array(
            'profile' => $profile,
            'auth' => $auth,
            'cash' => $cash,
            'presentation' => $presentation);
    }

    public function remove($user_id){}

    public function store($user_id){}
}