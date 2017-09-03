<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Application\Utility\DataTable;


Class ConverterHelper extends AbstractHelper {

// ====================== Date Conversion =============================================
    /**
     * Coverts saved time format to readable format
     * @param mixed $date timestamp | forms date format
     * @return string date in format 'd.m.Y'
     */
    public function myDate($date)
    {
        if ($date == 0 || $date == null) return '';
        //prepare forms date format .. array length 1 if timestamp
        $parts = explode('-', $date);
        //        "is timestamp"    ? create from timestamp  :  create from form date format
        return (count($parts) == 1 )? date( 'd.m.Y', $date ) : $parts[2] . '.' . $parts[1] . '.' .$parts[0];
    }

// ====================== Byte Conversion =============================================

    /**
     * beautifies a number of bytes
     * @param $bytes int
     * @return string
     */
    public function beautyBytes($bytes) {
        return $this->fileSizeConvert($bytes);
    }

    /**
     * Converts bytes into human readable file size.
     *
     * @param string $bytes
     * @return string human readable file size (2,87 Мб)
     * @author Mogilev Arseny
     */
    private function fileSizeConvert($bytes)
    {
        $bytes = floatval($bytes);
        $arBytes = array(
            0 => array(
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ),
            1 => array(
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ),
            2 => array(
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ),
            3 => array(
                "UNIT" => "KB",
                "VALUE" => 1024
            ),
            4 => array(
                "UNIT" => "B",
                "VALUE" => 1
            ),
        );
        $result = 0;
        foreach($arBytes as $arItem)
        {
            if($bytes >= $arItem["VALUE"])
            {
                $result = $bytes / $arItem["VALUE"];
                $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
                break;
            }
        }
        return $result;
    }
}