<?php

namespace HGC;

class RequestUtils
{

	/*
	 * _set_header_type
	 * Sets a headers content type
	 * @param: mime type e.g. application/JSON
	 * @param: expire in ms
	 */
	static private function _set_header_type($mimeType, $expire = null)
	{
		header('Pragma:' . 'public'); // required

		$current_date = strtotime('now');

		if (!is_null($expire)) {
			//calculate expiration
			$future_date = $current_date + $expire;
			$future_date_formated = date("r", $future_date);

			//set expiration
			header('Expires:' . $future_date_formated);
		}

		//set last modified
		$last_modified_date = $current_date;
		$last_modified_date_formatted = date("r", $last_modified_date);
		header('Last-Modified:' . $last_modified_date_formatted);

		header('Cache-Control:' . 'must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control:' . 'private'); // required for certain browsers
		header('Access-Control-Allow-Origin:' . '*');
		header('Content-type:' . $mimeType);
	}

	/*
	 * set_header
	 * Sets the header content type
	 * @param format e.g. application/JSON, text/XML
	 */
	static public function set_header($format, $expire = null)
	{

		switch (strtoupper($format)) {
			case 'HTML':
				self::_set_header_type('text/html', $expire);
				break;
			case 'JSON':
				self::_set_header_type('application/json', $expire);
				break;
			case 'XML':
				self::_set_header_type('text/xml', $expire);
				break;
			case 'JAVASCRIPT':
				self::_set_header_type('application/javascript', $expire);
				break;
			case 'JPG':
			case 'JPEG':
				self::_set_header_type('image/jpeg', $expire);
				break;
			case 'PLAIN':
			default:
				self::_set_header_type('text/plain', $expire);
				break;
		}
	}

	/*
	 * encode_object
	 * Encodes an object in the specified format
	 * @param object The object to be encoded
	 * @param format The format in which to encode the object.
	 */
	static public function encode_object($object, $format)
	{

		switch (strtoupper($format)) {
			case 'XML':
				return Utils::generate_xml_from_array($object);
				break;
			case 'SERIALISED':
			case 'SERIALIZED':
				return serialize($object);
				break;
			case 'TEXT':
			case 'JSON':
			default:
				return json_encode($object);
				break;
		}
	}
}