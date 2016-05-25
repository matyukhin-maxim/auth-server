<?php

/**
 * Created by PhpStorm.
 * User: Матюхин_МП
 * Date: 25.05.2016
 * Time: 7:56
 */
class Cipher {

	public static $method = 'aes-256-ctr';

	public static function encode($data, $secret, $serialize = false) {

		if ($serialize) $data = serialize($data);

		return openssl_encrypt(
			$data,
			self::$method,
			$secret,
			OPENSSL_ZERO_PADDING
		);
	}

	public static function decode($data, $secret, $serialize = false) {

		$plain = openssl_decrypt(
			$data,
			self::$method,
			$secret,
			OPENSSL_ZERO_PADDING
		);

		return $serialize ? unserialize($plain) : $plain;
	}

	public static function generate_token ($len = 32)
	{
		// Алфавит возможных символов
		$chars = array(
			'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm',
			'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
			'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
			'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
			'0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
		);
		shuffle($chars);
		$num_chars = count($chars) - 1;
		$token = '';
		// Генерируем необходимое количество рндомных символов
		for ($i = 0; $i < $len; $i++) $token .= $chars[mt_rand(0, $num_chars)];
		return $token;
	}
}