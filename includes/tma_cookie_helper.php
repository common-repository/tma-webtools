<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TMA;

/**
 * Description of tma_cookie_helper
 *
 * @author thmarx
 */
class TMA_COOKIE_HELPER {

	public static $DAY = null;
	public static $HOUR = null;
	public static $MINUTE = null;
	
	public static $COOKIE_REQUEST = "_tma_rid";
	public static $COOKIE_REQUEST_EXPIRE = null;
	public static $COOKIE_VISIT = "_tma_vid";
	public static $COOKIE_VISIT_EXPIRE = null;
	public static $COOKIE_USER = "_tma_uid";
	public static $COOKIE_USER_EXPIRE = null;
	public static $COOKIE_FINGERPRINT = "_tma_fp";
	public static $COOKIE_FINGERPRINT_EXPIRE = null;
	
	public function __construct() {
		if (self::$COOKIE_REQUEST_ESPIRE == null) {
			self::$DAY = 24 * 60 * 60 * 1000;
			self::$HOUR = 60 * 60 * 1000;
			self::$MINUTE = 60 * 1000;
			self::$COOKIE_REQUEST_EXPIRE = 3 * self::$MINUTE;
			self::$COOKIE_VISIT_EXPIRE = 1 * TMA_COOKIE_HELPER::$HOUR;
			self::$COOKIE_USER_EXPIRE = 1 * TMA_COOKIE_HELPER::$YEAR;
			self::$COOKIE_FINGERPRINT= 1 * TMA_COOKIE_HELPER::$YEAR;
		}
	}

	public static function getCookie($name, $value, $expire, $setNew=false) {
		if (isset($_COOKIE[$name])) {
			$value = $_COOKIE[$name];
		}
		if ($setNew) {
			setcookie($name, $value, $expire, '/');
		}
		
		return $value;
	}

}

class UUID {

	public static function v3($namespace, $name) {
		if (!self::is_valid($namespace))
			return false;

		// Get hexadecimal components of namespace
		$nhex = str_replace(array('-', '{', '}'), '', $namespace);

		// Binary Value
		$nstr = '';

		// Convert Namespace UUID to bits
		for ($i = 0; $i < strlen($nhex); $i+=2) {
			$nstr .= chr(hexdec($nhex[$i] . $nhex[$i + 1]));
		}

		// Calculate hash value
		$hash = md5($nstr . $name);

		return sprintf('%08s-%04s-%04x-%04x-%12s',
				// 32 bits for "time_low"
				substr($hash, 0, 8),
				// 16 bits for "time_mid"
				substr($hash, 8, 4),
				// 16 bits for "time_hi_and_version",
				// four most significant bits holds version number 3
				(hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x3000,
				// 16 bits, 8 bits for "clk_seq_hi_res",
				// 8 bits for "clk_seq_low",
				// two most significant bits holds zero and one for variant DCE1.1
				(hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
				// 48 bits for "node"
				substr($hash, 20, 12)
		);
	}

	public static function v4() {
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
				// 32 bits for "time_low"
				mt_rand(0, 0xffff), mt_rand(0, 0xffff),
				// 16 bits for "time_mid"
				mt_rand(0, 0xffff),
				// 16 bits for "time_hi_and_version",
				// four most significant bits holds version number 4
				mt_rand(0, 0x0fff) | 0x4000,
				// 16 bits, 8 bits for "clk_seq_hi_res",
				// 8 bits for "clk_seq_low",
				// two most significant bits holds zero and one for variant DCE1.1
				mt_rand(0, 0x3fff) | 0x8000,
				// 48 bits for "node"
				mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
	}

	public static function v5($namespace, $name) {
		if (!self::is_valid($namespace))
			return false;

		// Get hexadecimal components of namespace
		$nhex = str_replace(array('-', '{', '}'), '', $namespace);

		// Binary Value
		$nstr = '';

		// Convert Namespace UUID to bits
		for ($i = 0; $i < strlen($nhex); $i+=2) {
			$nstr .= chr(hexdec($nhex[$i] . $nhex[$i + 1]));
		}

		// Calculate hash value
		$hash = sha1($nstr . $name);

		return sprintf('%08s-%04s-%04x-%04x-%12s',
				// 32 bits for "time_low"
				substr($hash, 0, 8),
				// 16 bits for "time_mid"
				substr($hash, 8, 4),
				// 16 bits for "time_hi_and_version",
				// four most significant bits holds version number 5
				(hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000,
				// 16 bits, 8 bits for "clk_seq_hi_res",
				// 8 bits for "clk_seq_low",
				// two most significant bits holds zero and one for variant DCE1.1
				(hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
				// 48 bits for "node"
				substr($hash, 20, 12)
		);
	}

	public static function is_valid($uuid) {
		return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?' .
						'[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuid) === 1;
	}

}
