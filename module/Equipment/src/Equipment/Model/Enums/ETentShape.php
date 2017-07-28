<?php

namespace Equipment\Model\Enums;


abstract class ETentShape
{
    const ROUND = 0;
    const RECTANGLE = 1;
    const RECTANGLE_TWO_MAST = 2;
    const ROUND_RECTANGLE = 3;
    const A_TENT = 4;

    const TRANSLATION = array(
        0 => 'Rundzelt',
        1 => 'rechteckiges Zelt, Einmast',
        2 => 'rechteckiges Zelt, Zweimast',
        3 => 'verbreitertes Rundzelt, Zweimast(z.B. Sachs 4m x 6m)',
		4 => 'A-Tent, Eingang an der kurzen Seite',
    );
    const IMAGES = array(
        0 => '/img/tentDefaults/roundtent.png',
        1 => '/img/tentDefaults/squaretent.png',
        2 => '/img/tentDefaults/squaretentTwoMast.png',
        3 => '/img/tentDefaults/sachs.png',
		4 => '/img/tentDefaults/a_tent.png',
    );
}