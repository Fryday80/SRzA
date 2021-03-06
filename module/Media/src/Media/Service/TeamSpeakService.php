<?php
namespace Media\Service;

use Auth\Service\UserService;
use Media\Utility\ts3admin;


class TeamSpeakService {
    private $config;
    /** @var  ts3admin */
    public $tsAdmin;
    public $userService;

    function __construct($config, UserService $userService) {
        $this->config = $config;
        $this->userService = $userService;
		$this->tsAdmin = new ts3admin($config['ip'], $config['queryPort']);
    }
    public function connect() {
        if ($this->tsAdmin->isConnected()) return true;
        $connectionResult = $this->tsAdmin->connect();
        if($connectionResult['success']) {
            $this->tsAdmin->login($this->config['user'], $this->config['pass']);
            $this->tsAdmin->selectServer(9987);
            return true;
        } else {
            return $connectionResult['errors'];
        }
    }
    public function getChannels() {
        $channels = $this->tsAdmin->channelList("-topic -flags -limits -icon");
        $channelList = [];
        foreach ($channels['data'] as $channelInfo) {
            $channelList[$channelInfo['cid']] = $channelInfo;
        }
//        $this->sendMsgToServer(1, ":P :D :P :D   Ich will nur nerven   :P :D :P :D");
        return $this->buildChannelTree($channelList);
	}
	public function getClients() {
        $result = $this->tsAdmin->clientList("-uid -away -times -groups -info -country -icon -ip");
        if (!$result['success']) {
            return [];
        }
        //@todo map clients to users

		// remove ServerQuery as client
        $data = $result['data'];
        $result['data'] = array();
        foreach ($data as $client){
        	if ($client['client_version'] !== "ServerQuery")
        		array_push($result['data'], $client);
		}

        return $result['data'];
    }

	/**
	 * Get Clients INCLUDING the Server Query
	 * @return array
	 */
	public function getAllClients() {
		$result = $this->tsAdmin->clientList("-uid -away -times -groups -info -country -icon -ip");
		if (!$result['success']) {
			return [];
		}
		//@todo map clients to users
		return $result['data'];
	}
    public function sendMsgToClient($clientID, $msg) {
        $this->tsAdmin->sendMessage(1, $clientID, $msg);
    }
    public function sendMsgToChannel($channelID, $msg) {
        $this->tsAdmin->sendMessage(2, $channelID, $msg);
    }
    public function sendMsgToServer($serverID, $msg) {
        $this->tsAdmin->sendMessage(3, $serverID, $msg);
    }
    public function sendGMMsg($msg) {
        $this->tsAdmin->gm($msg);
    }
    private function buildChannelTree($ar, $pid = 0)
    {
        $op = array();
        foreach ($ar as $item) {
            if ($item['pid'] == $pid) {
                $op[] = $item;
                $children = $this->buildChannelTree($ar, $item['cid']);
                if ($children) {
                    $keyId = max(array_keys($op));
                    $op[$keyId]['children'] = $children;
                }
            }
        }
        return $op;
    }

	public function getImageConfig()
	{
		return $this->config['Images'];
	}
}