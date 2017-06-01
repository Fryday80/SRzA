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

class InlineFromFile extends AbstractHelper
{
    private $types = [
        'js' => 'text/javascript',
        'css' => 'text/css'
    ];
    public function __invoke($module, $controller = null, $name = null, $arguments = null) {
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
        echo $this->createHTML($extension, $content, $arguments);
    }
    private function createHTML($extension, $content, $arguments = null) {
        $result = '';
        switch($extension) {
            case 'js':
                $result = '<script type="';
                $result .= $this->types[$extension].'">';
                $result .= '(function(){';
                if (is_array($arguments)) {
                    $result .= 'var args = ';
                    $result .= json_encode($arguments);
                    $result .= ';';
                } else {
                    $result .= 'var args = [];';
                }
                $result .= $content;
                $result .= '})()';
                $result .= '</script>';
                break;
            case 'css':
                $result = '<style>';
                $result .= $content;
                $result .= '</style>';
                break;
        }
        return $result;
    }
}