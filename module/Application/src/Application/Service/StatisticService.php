<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 13.04.2017
 * Time: 02:13
 */

namespace Application\Service;


class StatisticService
{
    private $sm;
    private $activeUsers ;
    private $pageHits;
    private $systemLog;

    function __construct($sm)
    {
        $this->sm = $sm;
        $this->activeUsers
        $this->pageHits
        $this->systemLog
    }
}