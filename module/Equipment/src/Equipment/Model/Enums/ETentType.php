<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 02.07.2017
 * Time: 15:23
 */

namespace Equipment\Model\Enums;


abstract class ETentType
{
    const H0 = 0;
    const H60 = 1;
    const H120 = 2;
    const H180 = 3;
    const H200 = 4;

    const TRANSLATE_TO_STRING = array(
        0 => 'keine Seitenwand',
        1 => '60cm Seitenwand',
        2 => '120cm Seitenwand',
        3 => '180cm Seitenwand',
        4 => '200cm Seitenwand',
    );
}