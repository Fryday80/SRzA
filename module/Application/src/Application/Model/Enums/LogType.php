<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 23.04.2017
 * Time: 19:56
 */

namespace Application\Model\Enums;


abstract class LogType {
    const ERROR_GUEST = 0;
    const ERROR_MEMBER = 1;
    const ERROR_LOG_IN = 2;
}