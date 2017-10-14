<?php
namespace Media\Controller;


use Auth\Service\AclService;
use Auth\Service\UserService;
use Media\Service\MediaService;
use Media\Service\TeamSpeakService;
use Media\Utility\ts3admin;
use Zend\Mvc\Controller\AbstractActionController;

class TeamSpeakController extends AbstractActionController  {

    /** @var  TeamSpeakService */
    private $tsService;
    /** @var UserService  */
    private $userService;

    private $imageConfig;
	private $channelImage;
	private $clientImage;
	private $tsConnectionState;
	private $clientsOnline = array();
	private $clientsOnlineCount = 0;
	private $channels = array();

	public function __construct(TeamSpeakService $tsService, UserService $userService)
    {
        $this->tsService = $tsService;
        $this->userService = $userService;
        $this->imageConfig = $this->tsService->getImageConfig();
		$connection = $this->tsService->connect();
		$this->tsConnectionState = ($connection === true) ?: false;
		bdump($this->tsConnectionState);
		if ($this->tsConnectionState){
			$this->clientsOnline = $this->tsService->getClients();
			$this->clientsOnlineCount = count($this->clientsOnline);
			$this->channels = $this->tsService->getChannels();
		}
		$this->setImages();
    }

    public function indexAction()
    {

    	return array(
    		'tsConnectionState' => $this->tsConnectionState,
			'channelImage' => $this->channelImage,
			'clientImage' => $this->clientImage,
			'channels' => $this->channels,
			'clientsOnline' => $this->clientsOnline,
			'css' => $this->css(),
		);
//		// And get the widget for the invoice
//		$invoiceId         = $this->params('id');
//		$invoiceWidget = $this->forward()->dispatch('Application\Controller\Invoice', array(
//			'action' => 'display',
//			'id'     => $invoiceId
//		));
//
//		$mainViewModel = new ViewModel(array(
//			'ganzNormal' => 42
//		));
//		return $mainViewModel->addChild($invoiceWidget, 'invoiceWidget');
    }

	/**
	 * @param string $channel path of channel image
	 * @param string $client  path of client image
	 */
	private function setImages()
	{
		$channel = (isset($this->imageConfig['channelImage'])) ? $this->imageConfig['channelImage'] : null;
		$client  = (isset($this->imageConfig['clientImage']))  ? $this->imageConfig['clientImage']  : null;
		$pStartClass = '<img class="';
		$pSRC = '" src="';
		$pALT = '" alt="';
		$pEnd = '">';

		$this->channelImage = ($this->validImage($channel)) ? $pStartClass . 'ts3-channel-image' . $pSRC . $channel . $pALT . 'CHANNEL: ' . $pEnd : 'CHANNEL: ';

		$this->clientImage = ($this->validImage($client)) ? $pStartClass . 'ts3-client-image'  . $pSRC . $client  . $pALT . 'CLIENT: ' . $pEnd : 'CLIENT: ';
	}

	private function validImage($imagePath)
	{
		if (  !$imagePath
			|| $imagePath === null
			|| $imagePath === ''
			|| !is_string($imagePath)
		) return false;
		return true;
	}

	private function css()
	{
		$css = '<style>';
		switch ($this->clientsOnlineCount < 1) {
			case true:
				$css .= '.ts3-channel-image {
							background: none;
							height: 50px;
						} ';
				break;
			case false:
				$css .= 'ul.ts3-channel, ul.ts3-channel * {
							list-style: none;
						}';
				$css .= '.ts3-channel-image {
							background: #fff;
							height: 20px;
						} ';
				$css .= '.ts3-channel {
							background: #fff;
						} ';
				$css .= '.ts3-client-image {
							background: lightblue;
							height: 20px;
							margin-right: 5px;
						} ';
				$css .= '.ts3-client {
							background: lightblue;
							margin-left: 20px;
						} ';
				break;
		}
		$css .= '</style>';
		return $css;
	}
}