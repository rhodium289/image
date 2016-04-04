<?php
/**
 * @file Utils
 * Provides a library of functions used across the modules
 * Some functions can be moved into a more specific location if necessary
 *
 */

namespace HGC;

class Utils {
	public static function array_key_empty($key, $array) {
		return (!is_array($array) || !array_key_exists($key, $array) || empty($array[$key]));
	}
	
	private static $_instance = NULL;
	
	private function __construct() {

	}
	
	public static function getInstance() {
			
		if (!isset(self::$_instance)) {
			self::$_instance = new Utils();
			self::$_instance->_init();
		}
		
		return self::$_instance;
	}
	
	private function _init() {
		
	}

	/*
	 * generate_xml_from_array
	 * @param: array
	 * @return: XML
	 */
	public static function generate_xml_from_array($argArray) {
		// function defination to convert array to xml
		function array_to_xml($argArray, $argXml) {
		    foreach($argArray as $key => $value) {
		        if(is_array($value)) {
		            if(!is_numeric($key)){
		                $subnode = $argXml->addChild("$key");
		                array_to_xml($value, $subnode);
		            }
		            else{
		            	$subnode = $argXml->addChild('element');
		            	$subnode->addAttribute('id', $key);
		                array_to_xml($value, $subnode);
		            }
		        }
		        else {
		            $argXml->addChild("$key","$value");
		        }
		    }
		}

		// Create parent XML Element
		$xml = new SimpleXMLElement('<result></result>');

	    // function call to convert array to xml
		array_to_xml($argArray,$xml);
		
		// generate XML
		return $xml->asXML();
	}
}