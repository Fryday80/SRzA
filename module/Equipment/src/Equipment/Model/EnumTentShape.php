<?php

namespace Equipment\Model;


abstract class EnumTentShape
{
    const ROUND = 0;
    const RECTANGLE = 1;
    const RECTANGLE_TWO_MAST = 2;
    const ROUND_RECTANGLE = 2;

    const TRANSLATION = array(
        0 => 'Rundzelt',
        1 => 'rechteckiges Zelt Einmast',
        2 => 'rechteckiges Zelt Zweimast',
        3 => 'verbreitertes Rundzelt Zweimast(z.B. Sachs 4m x 6m)'
    );
    const IMAGINATION = array(
        0 => '<img alt="' . self::TRANSLATION[0] . '" src="/img/roundtent.png" style="width: 50px">',
        1 => '<img alt="' . self::TRANSLATION[1] . '" src="/img/squaretent.png" style="width: 50px">',
        2 => '<img alt="' . self::TRANSLATION[1] . '" src="/img/squaretentTwoMast.png" style="width: 50px">',
        3 => '<img alt="' . self::TRANSLATION[2] . '" src="/img/sachs.png" style="width: 50px">',
    );
}