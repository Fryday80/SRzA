<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 23.04.2017
 * Time: 19:56
 */

namespace Application\Model\Enums;


abstract class HitType {
    const MEMBER = 0;
    const GUEST = 1;
    const ERROR_MEMBER = 2;
    const ERROR_GUEST = 3;
    const TYPES_COUNT = 4;//actually no type. keep it at bottom with the highest int
}