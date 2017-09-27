<?php
namespace Media\Service;

use Media\Utility\ts3admin;


class TeamSpeakService {
    private $config;
    /** @var  ts3admin */
    public $tsAdmin;
    function __construct($config) {
        $this->config = $config;
		bdump($config['ip']);

        $ts3_ip = $config['ip'];
        $ts3_queryport = 10011;
        $ts3_user = 'serveradmin';
        $ts3_pass = 'password';
		$this->tsAdmin = new ts3admin($ts3_ip, $ts3_queryport);
    }
    public function getChannels() {
    	//@todo check if connected
		return $this->tsAdmin->channelList();
	}

}