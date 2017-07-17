<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 23.04.2017
 * Time: 19:56
 */

namespace Application\Model\Enums;

abstract class CounterType {
    const MEMBER = 0;
    const GUEST = 1;
    const ERROR_MEMBER = 2;
    const ERROR_GUEST = 3;
    const ALL = 4;
    const ERROR = 5;
}