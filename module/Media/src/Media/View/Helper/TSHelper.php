<?php
	/**
	 * Created by IntelliJ IDEA.
	 * User: Fry
	 * Date: 28.09.2017
	 * Time: 10:45
	 */

	namespace Media\View\Helper;


	use Media\Service\TeamSpeakService;
	use Media\Utility\ts3admin;
	use Zend\View\Helper\AbstractHelper;

	class TSHelper extends AbstractHelper
	{
		protected $config;
		/** @var TeamSpeakService  */
		protected $tsService;
		/** @var  ts3admin */
		protected $ts3;

		protected $channelImage; // channel image
		protected $clientImage; // user/client image

		public function __construct($config, TeamSpeakService $tsService)
		{
			$this->config = $config;
			$this->tsService = $tsService;
			$this->ts3 = &$this->tsService->tsAdmin;
			$this->tsService->connect();
			$this->setImages();
		}

		public function getChannels()
		{
			return '<br/>'
				. $this->css()
				. $this->renderChannelList($this->tsService->getChannels(), $this->tsService->getClients())
				;
		}

		protected function css(){
			$css = '<style>';
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
			$css .= '</style>';

			return $css;
		}

		protected function renderClientList($channelID, $clients) {
			$clientList = '';
			foreach ($clients as $client) {
				if ($client['cid'] !== $channelID) continue;
				$clientList .= '<li class="ts3-client">';
				$clientList .= $this->clientImage;
				$clientList .= $client['client_nickname'];
				$clientList .= '</li>';
			}
			return $clientList;
		}

		protected function renderChannelList($channels, $clients) {
			$channelList = '<ul class="ts3-channel">';
			foreach ($channels as $channel) {
				$channelList .= '<li class="ts3-channel">';
				$channelList .= $this->channelImage;
				$channelList .= $channel['channel_name'];
				$channelList .= ' ('. $channel['total_clients'] .')';
				$channelList .= '<ul>';
				$channelList .= $this->renderClientList($channel['cid'], $clients);
				if (isset($channel['children']) && is_array($channel['children'])) {
					$channelList .= $this->renderChannelList($channel['children'], $clients);
				}
				$channelList .= '</ul>';
				$channelList .= '</li>';
			}
			$channelList .= '</ul>';
			return $channelList;
		}

		/**
		 * @param string $channel path of channel image
		 * @param string $client  path of client image
		 */
		private function setImages()
		{
			$channel = (isset($this->config['channelImage'])) ? $this->config['channelImage'] : null;
			$client  = (isset($this->config['clientImage']))  ? $this->config['clientImage']  : null;
			$pStartClass = '<img class="';
			$pSRC = '" src="';
			$pALT = '" alt="';
			$pEnd = '">';

			$this->channelImage = ($this->validImage($channel)) ? $pStartClass . 'ts3-channel-image' . $pSRC . $channel . $pALT . ' ' . $pEnd : 'CHANNEL: ';

			$this->clientImage = ($this->validImage($client)) ? $pStartClass . 'ts3-client-image'  . $pSRC . $client  . $pALT . 'C' . $pEnd : 'CLIENT: ';
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
	}