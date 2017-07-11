<?php
namespace Cast\Form;

use Cast\Service\BlazonService;
use Zend\Form\Form;

class JobForm extends Form
{
    public function __construct($blazonService)
    {
        parent::__construct("Job");
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'job',
            'type' => 'text',
            'options' => array(
                'label' => 'Job Name'
            )
        ));
        $this->add(array(
            'name' => 'blazon_id',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
            ),
            'options' => array(
                'disable_inarray_validator' => true,
                'label' => 'Wappen Overlay',
                'value_options' => $blazonService->getBlazonListOverlays(),
            )
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Submit',
            )
        ));
        if ($blazonService) $this->setBlazonsForSelect($blazonService);
    }

    /**
     * @param BlazonService $blazonService
     */
    private function setBlazonsForSelect($blazonService) {
        $liCssTemplate = <<<EOD
    background-image:url('%s');
    height: 55px;
    background-size: 45px;
    background-repeat: no-repeat;
    background-position: 5px 5px;
    padding-left: 55px;
    padding-top: 5px;
EOD;
        $data = [];

        array_push($data, array(
                'attributes'=> [
                    'data-li-style' => '',
                    'selected'
                ],
                'value' => 0,
                'label' => 'Keins'
            )
        );
        foreach ($blazonService->getAllOverlays() as $value) {
            $blazonUrl = '/media/file/wappen/'.$value['filename'];
            $liCss = sprintf($liCssTemplate, $blazonUrl);
            array_push($data, array(
                    'attributes'=> [
                        'data-li-style' => $liCss
//                        'data-li-class' => '',
//                        'data-span-style' => 'background-image:url('.$blazonUrl.'); height: 40px; width: 40px; background-size: 30px',
//                        'data-span-class' => ''
                    ],
                    'value' => $value['id'],
                    'label' => $value['name']
                )
            );
        }
//        <option disabled selected>Please pick one</option>
        $this->get('blazon_id')->setValueOptions($data);
    }
}