<?php

namespace Equipment\Model;


abstract class EnumTentShape
{
    const ROUND = 0;
    const RECTANGLE = 1;

    const TRANSLATION = array(
        0 => 'Rundzelt',
        1 => 'rechteckiges Zelt',
    );
}