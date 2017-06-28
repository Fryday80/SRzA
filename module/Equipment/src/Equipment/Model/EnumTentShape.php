<?php

namespace Equipment\Model;


abstract class EnumTentShape
{
    const ROUND = 0;
    const RECTANGLE = 1;
    const RECTANGLE_TWO_MAST = 2;
    const ROUND_RECTANGLE = 3;

    const TRANSLATION = array(
        0 => 'Rundzelt',
        1 => 'rechteckiges Zelt, Einmast',
        2 => 'rechteckiges Zelt, Zweimast',
        3 => 'verbreitertes Rundzelt, Zweimast(z.B. Sachs 4m x 6m)'
    );
    const IMAGES = array(
        0 => '/img/roundtent.png',
        1 => '/img/squaretent.png',
        2 => '/img/squaretentTwoMast.png',
        3 => '/img/sachs.png',
    );
}