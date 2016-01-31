<?php

/**
 * Created by PhpStorm.
 * User: fellix
 * Date: 28.01.16
 * Time: 20:51
 */

function e_code($line) {

	$line = trim(mb_convert_encoding($line, 'windows-1251', 'utf-8'));

	$result = '';
	for ($cnt = 0; $cnt < strlen($line); $cnt++) {
		$result .= dechex(ord($line[$cnt])) . ' ';//(($w[$cnt])) .'-';
	}

	charsetChange($result);
	//$result = strtoupper($result);
	echo $result . "<br/>";
}

function d_code($line) {

	$line = trim(mb_convert_encoding($line, 'windows-1251', 'utf-8'));

	$words = preg_split('/\s+/', $line);

	$line = '';
	foreach ($words as $w) {
		$line .= chr(hexdec($w));
	}

	charsetChange($line);
	echo str_replace(' ', '&nbsp;', $line) . "<br/>";
}

class SitesController extends CController {

	public function actionIndex() {

		//if (!$this->authdata) $this->redirect('/login/');
		$this->scripts[] = 'esteregg';
		$this->render('panel');
	}

	public function ajaxHappy() {

		$text = filter_input(INPUT_POST, 'text', FILTER_SANITIZE_STRING);
		$action = filter_input(INPUT_POST, 'action', FILTER_VALIDATE_BOOLEAN);


		$method = $action ? 'e_code' : 'd_code';
		array_map($method, explode("\n", $text));
		//var_dump($lines);
	}

}