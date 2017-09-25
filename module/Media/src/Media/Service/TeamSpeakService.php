<?php
namespace Media\Service;

use Media\Utility\ts3admin;


class TeamSpeakService {
    private $config;
    /** @var  ts3admin */
    public $tsAdmin;
    function __construct($config) {
        $this->config = $config;


        $ts3_ip = '127.0.0.1';
        $ts3_queryport = 10011;
        $ts3_user = 'serveradmin';
        $ts3_pass = 'password';
        $this->tsAdmin = new ts3admin($ts3_ip, $ts3_queryport);

    }

}