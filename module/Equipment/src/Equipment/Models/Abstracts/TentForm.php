<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 24.06.2017
 * Time: 11:59
 */

namespace Equipment\Models\Abstracts;


abstract class TentForm
{

    const ROUND = 0;
    const SQUARE = 1;
    const RECTANGLE = 2;

    static function translateConst($const){
        if ($const == self::ROUND) return 'Rundzelt';
        if ($const == self::SQUARE) return 'quadratisches Zelt';
        if ($const == self::RECTANGLE) return 'rechteckiges Zelt';
    }
}