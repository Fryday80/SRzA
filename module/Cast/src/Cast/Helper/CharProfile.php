<?php
namespace Cast\Helper;

use Zend\View\Helper\AbstractHelper;
use Auth\Model\AuthStorage;

class CharProfile extends AbstractHelper
{

    public function __construct() {
        return $this;
    }
    public function __invoke()
    {
        return $this;
    }
    public function small($char) {
        $return = '<ul>'.
                    '   <li>Name: '.$char['name'].'</li>'.
                    '   <li>surename '.$char['surename'].'</li>'.
                    '   <li>Beruf: '.$char['job_name'].'</li>'.
                        '   <div class="banner_small_sign" ';
        foreach ($char as $key => $value)
        {
            $return .= $this->addData($key, $value);
        }
        $return .=      '</div>'.
                        '   <li>Vita: '.$char['vita'].'</li>'.
                        '   <li>&nbsp;</li>'.
                        '   <li>Banner image</li>'.
                '</ul>';
            return $return;
    }
    private function addData($key, $value)
    {
        if ($key == 'id')return 'id="'. $value .'" ';
        if ($key == 'vita') return;
        else return 'data-'.$key.'=" ' . $value . '" ';
    }
}
