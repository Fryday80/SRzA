<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 24.06.2017
 * Time: 11:57
 */

namespace Equipment\Models\DataObjects\Single;


use Equipment\Models\Abstracts\TentForm;

class Tent
{
    protected $form;
    protected $width;
    protected $length;


    public function getFormString()
    {
        return TentForm::translateConst($this->form);
    }

    public function getForm()
    {
        return $this->form;
    }

    public function setForm($formType)
    {
        $this->form = $formType;
    }

}