<?php
namespace Cast\Helper;

use Zend\View\Helper\AbstractHelper;
use Auth\Model\AuthStorage;

class CharProfile extends AbstractHelper
{
    private $data;

    public function __construct() {
        return $this;
    }
    public function __invoke()
    {
        return $this;
    }
    public function small($char) {
        return '<ul>'.
            '   <li>Name: '.$char['name'].'</li>'.
            '   <li>surename '.$char['surename'].'</li>'.
            '   <li>Beruf: '.$char['job_name'].'</li>'.
            '   <li>Vita: '.$char['vita'].'</li>'.
            '   <li>&nbsp;</li>'.
            '   <li>Banner image</li>'.
            '</ul>';
    }
}
