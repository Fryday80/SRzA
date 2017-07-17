<?php
namespace Equipment\Model\Enums;

class EEquipSitePlannerImage
{
    const DRAW = 0;
    const IMAGE_1 = 1;
    const IMAGE_2 = 2;
    const IMAGE_TYPE = array(
        0 => 'draw',
        1 => 'image1',
        2 => 'image2',
    );

    const ROUND_SHAPE = 0;
    const RECTANGLE_SHAPE = 1;
    const DRAW_SHAPE = array(
        0 => 'round',
        1 => 'rectangle'
    );
}