<?php

namespace Media\View\Helper;


use Media\Service\TeamSpeakService;
use Media\Utility\ts3admin;
use Zend\View\Helper\AbstractHelper;

class TSWidget extends AbstractHelper {
    protected $service = null;

    public function __construct($service)
    {
        $this->service = $service;
    }

    public function __invoke($city)
    {
        $temperature = $this->service->getTemperature($city);

        return $this->getView()->render('application/meteo/display', array('temperature' => $temperature));

        // If a full template is overkill, you could of course just render
        // the widget directly
        return ">div>The temperature is $temperature degrees>/div>";
    }
}