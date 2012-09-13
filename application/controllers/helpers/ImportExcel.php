<?php

require_once APPLICATION_PATH . '/../library/PHPExcel/PHPExcel.php';

class Application_Action_Helper_ImportExcel extends Zend_Controller_Action_Helper_Abstract {

    function direct(Zend_Controller_Action $controller, $filepath, $formclass) {
        // Read the template file
        $ext = substr(strrchr($filepath, '.'), 1);
        if ($ext === 'xlsx') {
            $inputFileType = 'Excel2007';
        } else {
            $inputFileType = 'Excel5';
        }
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($filepath);
        $objWorksheet = $objPHPExcel->getActiveSheet();

        $i = 0;
        $k = 0;
        $objectsvalues = array();
        if($objWorksheet->getTitle() === 'elkeimport') {
            foreach ($objWorksheet->getRowIterator() as $row) {
                $i++;
                if ($i <= 3) {
                    continue;
                }
                $form = new $formclass($controller->view);
                $postvalues = $this->parseRow($row, $objWorksheet);
                if ($form->isValid($postvalues)) {
                    $objectsvalues[$k] = $form->getValues();
                } else {
                    return array('error' => true, 'errorRow' => $i, 'formElements' => $form->getElementsAsArray());
                }
                $k++;
            }
            return $objectsvalues;
        } else { // Ο χρήστης εισήγαγε άσχετο excel
            return array('error' => true, 'errorRow' => 1, 'formElements' => array(), 'message' => 'Το αρχείο δεν ακολουθεί τη μορφή του template.');
        }
    }

    protected function parseRow($row, $objWorksheet) {
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);
        $resultArray = array();
        foreach ($cellIterator as $cell) {
            $value = $cell->getValue();
            $dataType = $cell->getDataType();
            $formatCode = $objWorksheet->getStyle($cell->getColumn() . $cell->getRow())->getNumberFormat()->getFormatCode();
            $fieldName = $objWorksheet->getCell($cell->getColumn() . '1')->getValue();
            if ($dataType === 'n' && $formatCode !== 'General') { // Ημερομηνία
                $value = PHPExcel_Style_NumberFormat::toFormattedString($value, "D/M/YYYY");
            }
            $resultArray = ArrayHandler::merge_array($resultArray, ArrayHandler::convertStringToMultiDimArray($fieldName, $value));
        }
        return $resultArray;
    }

}

class ArrayHandler {

    // http://stackoverflow.com/questions/3387472/string-to-variable-depth-multidimensional-array
    public static function convertStringToMultiDimArray($string, $value) {
        $result = array();
        $keys = strpos($string, '-') !== false ? explode('-', $string) : array($string);
        $ptr = &$result;
        foreach ($keys as $k) {
            if (!isset($ptr[$k])) {
                $ptr[$k] = array();
            }
            $ptr = &$ptr[$k];
        }
        if (empty($ptr)) {
            $ptr = $value;
        } else {
            $ptr[] = $value;
        }
        return $result;
    }

    //Recursive method to merge array for multi dimensional, continues till it reaches its depth
    // http://blog.sachinkraj.com/how-to-merge-two-multidimensional-arrays-in-php/
    public static function merge_array($array1, $array2) {
        if (is_array($array2) && count($array2)) {
            foreach ($array2 as $m => $n) {
                if (is_array($n) && count($n)) {
                    if (!isset($array1[$m])) {
                        $array1[$m] = null;
                    }
                    $array1[$m] = static::merge_array($array1[$m], $n);
                } else {
                    $array1[$m] = $n;
                }
            }
        } else {
            $array1 = $array2;
        }
        return $array1;
    }

    // Implode a multidimensional array.The $glue may be either an array or a string.
    // If array, each element will be used as glue for each level of $pieces.
    // If count($glue) is less than number of levels of $pieces, the last glue will be used for the remainder levels.
    // http://php.net/manual/en/function.implode.php
    function implode_r($glue, $pieces) {
        $return = "";

        if (!is_array($glue)) {
            $glue = array($glue);
        }

        $thisLevelGlue = array_shift($glue);

        if (!count($glue))
            $glue = array($thisLevelGlue);

        if (!is_array($pieces)) {
            return (string) $pieces;
        }

        foreach ($pieces as $sub) {
            $return .= implode_r($glue, $sub) . $thisLevelGlue;
        }

        if (count($pieces))
            $return = substr($return, 0, strlen($return) - strlen($thisLevelGlue));

        return $return;
    }

    /**
     * RayArray arrays utility class
     *
     * This class provides configuration array handling funcionalities,
     * may be usefull when dealing with configuration data.
     *
     * Usage: using this class you can convert a multidimensional configuration
     * array into a single dimension array ready to store into sql table/ flat file.
     *
     * methods available are
     *  - shorten() - static
     *  - unshorten() - static
     *  - subarray() - static
     *
     * @package     raynux
     * @subpackage  raynux.lab.array
     * @version     1.0
     * @author      Md. Rayhan Chowdhury
     * @email       ray@raynux.com
     * @website     www.raynux.com
     * @license     GPL
     *
     * Shorten an multidimensional array into a single dimensional array concatenating all keys with separator.
     *
     * @example array('country' => array(0 => array('name' => 'Bangladesh', 'capital' => 'Dhaka')))
     *          to array('country.0.name' => 'Bangladesh', 'country.0.capital' => 'Dhaka')
     *
     * @param array $inputArray, arrays to be marged into a single dimensional array
     * @param string $path, Default Initial path
     * @param string $separator, array key path separator
     * @return array, single dimensional array with key and value pair
     * @access public
     * @static
     */
    // http://raynux.com/blog/2009/06/28/convert-a-multidimensional-array-into-single-dimension/
    static public function shorten(array $inputArray, $path = null, $separator = "-") {
        $data = array();
        if (!is_null($path)) {
            $path = $path . $separator;
        }

        if (is_array($inputArray)) {
            foreach ($inputArray as $key => &$value) {
                if (!is_array($value)) {
                    $data[$path . $key] = $value;
                } else {
                    $data = array_merge($data, self::shorten($value, $path . $key, $separator));
                }
            }
        }

        return $data;
    }

}

?>