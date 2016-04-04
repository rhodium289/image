<?php

namespace jds\common;

class Assert {
	static function hexadecimalInteger($argHexString) {
		$hexValue=(int)('0x'.$argHexString);
		self::integer($hexValue);
	}
	
	static function hexadecimalBetween($argHexString, $argMin, $argMax) {
		$hexValue=(int)('0x'.$argHexString);
		self::between($hexValue, $argMin, $argMax);
	}
	
	static function integer($argInt) {
		if (!is_numeric($argInt)) {
			throw new Exception($argInt.' is not an Integer');
		}
	}
	
	static function between($argInt, $argMin, $argMax) {
		if (!is_numeric($argInt)) {
			throw new Exception($argInt.' is not an Integer');
		}
		if ($argInt<$argMin || $argInt>$argMax) {
			throw new Exception($argInt.' is out of range ['.$argMin.'..'.$argMax.']');
		}
	}

	static function true($argBool) {
		if (!is_bool($argBool)) {
			throw new Exception($argBool.' is not a Boolean');	
		}
		if (!$argBool) {
			throw new Exception(($argBool?'true':'false').' is not true');
		}
	}

	static function false($argBool) {
		if (!is_bool($argBool)) {
			throw new Exception($argBool.' is not a Boolean');	
		}
		if ($argBool) {
			throw new Exception(($argBool?'true':'false').' is not false');
		}
	}

	static function isarray($argArray) {
		if (!is_array($argArray)) {
			throw new Exception($argArray.' is not a Array');	
		}
	}
}