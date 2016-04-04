<?php

/**
 * Trims a entire array recursively.
 *
 * @author      Jonas John
 * @version     0.2
 * @link        http://www.jonasjohn.de/snippets/php/trim-array.htm
 * @param       array      $Input      Input array
 */
function TrimArray($Input){
 
    if (!is_array($Input))
        return trim($Input);
 
    return array_map('TrimArray', $Input);
}

// simple conversions from XML to array
function getArrayFormOfXml($argXml) {
	$json = json_encode($argXml);
    $array = json_decode($json,TRUE);

    $array=TrimArray($array);
    return $array;
}