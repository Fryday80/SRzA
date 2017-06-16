<?php
namespace Cast\View\Helper;

class CharDateHelper
{
    public function __invoke($date)
    {
        if ($date == 0 || $date == null) return '';
        $parts = explode('-', $date);
        return $parts[2] . '.' . $parts[1] . '.' .$parts[0];
    }
}