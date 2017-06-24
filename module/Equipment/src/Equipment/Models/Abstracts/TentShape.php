<?php

namespace Equipment\Models\Abstracts;


abstract class TentShape
{
    const OTHER = 0;
    const ROUND = 1;
    const SQUARE = 2;
    const RECTANGLE = 3;
    const TRANSLATION = array(
        0 => 'Sonstige',
        1 => 'Rundzelt',
        2 => 'quadratisches Zelt',
        3 => 'rechteckiges Zelt',
    );

    static function translateFromConst($const){
        if ($const == self::ROUND)     return self::TRANSLATION[$const];
        if ($const == self::SQUARE)    return self::TRANSLATION[$const];
        if ($const == self::RECTANGLE) return self::TRANSLATION[$const];
        else return self::TRANSLATION[$const];
    }

    static function translateToConst($string){
        if (self::hasType($string)){
            return array_search($string, self::TRANSLATION);
        }
        return self::OTHER;
    }

    static function hasType($string){
        if (in_array($string, self::TRANSLATION))return true;
        return false;
    }
}