<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 20.11.2016
 * Time: 02:00
 */

namespace Application\View\Helper;


use Exception;
use Zend\View\Helper\AbstractHelper;

class InlineJS extends AbstractHelper
{
    private $types = [
        'js' => 'text/javascript',
    ];
    public function __invoke($module, $controller = null, $name = null) {
        if (!$controller) {
            $path = $module;
        } else {
            $path = getcwd().'/module/'.ucfirst($module).'/view/'.strtolower($module).'/'.strtolower($controller).'/'.$name;
        }

        $extension = pathinfo($name, PATHINFO_EXTENSION);

        if (!file_exists($path)) {
            throw new Exception("InlineJs: Path '$path' not exists");
        }
        $content = file_get_contents($path);
        if (!$content) {
            throw new Exception("InlineJs: Error while loading file '$path'");
        }
        $result = '<script type="';
        $result .= $this->types[$extension].'">';
        $result .= $content;
        $result .= '</script>';
        return $result;
    }
}