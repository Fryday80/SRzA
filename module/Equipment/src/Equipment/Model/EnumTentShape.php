<?php

namespace Equipment\Model;


abstract class EnumTentShape
{
    const ROUND = 0;
    const RECTANGLE = 1;
    const ROUND_RECTANGLE = 2;

    const TRANSLATION = array(
        0 => 'Rundzelt',
        1 => 'rechteckiges Zelt',
        2 => 'verbreitertes Rundzelz (z.B. Sachs 4m x 6m)'
    );
}