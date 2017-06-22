<?php
namespace Application\Utility;


class URLModifier
{
    private $normal = array(
        " ", "ä", "Ä", "ö", "Ö", "ü", "Ü",
    );
    private $URLish = array(
        "-", "ae", "Ae", "oe", "Oe", "ue", "Ue",
    );

    public function toURL($string)
    {
        return str_replace($this->normal, $this->URLish, $string);
    }

    public function fromURL($url)
    {
        return str_replace($this->URLish, $this->normal, $url);
    }
}