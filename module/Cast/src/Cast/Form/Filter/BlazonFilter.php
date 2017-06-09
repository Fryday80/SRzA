<?php
namespace Cast\Form\Filter;

use Zend\InputFilter\InputFilter;

class BlazonFilter extends InputFilter
{

    public function __construct($filterFlag = null)
    {
        $this->common();
        $flag = strtolower($filterFlag);
        switch($flag) {
            case 'edit':
                $this->editFilter();
                break;
            default:
        }
    }

    private function common()
    {
        // name
        $this->add(array(
                'name' => 'name',
                'required' => true,
            )
        );
        // blazon
        $this->add(array(
                'name' => 'blazon',
                'target'    => './temp/',
                'use_upload_name ' => true,
                'randomize' => true,
                'required' => true,
            )
        );
        // blazonBig
        $this->add(array(
                'name' => 'blazonBig',
                'target'    => './temp/',
                'use_upload_name ' => true,
                'randomize' => true,
                'required' => false,
            )
        );
    }

    private function editFilter()
    {
        // blazon
        $this->add(array(
                'name' => 'blazon',
                'target'    => './temp/',
                'use_upload_name ' => true,
                'randomize' => true,
                'required' => false,
            )
        );
    }
}
