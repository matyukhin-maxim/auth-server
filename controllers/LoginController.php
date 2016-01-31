<?php

/**
 * Created by PhpStorm.
 * User: fellix
 * Date: 28.01.16
 * Time: 20:52
 */

/** @property LoginModel $model */
class LoginController extends CController {

	public function actionIndex() {

		$this->scripts[] = 'auth';

		Session::del('auth');
		$this->render('form');
	}

	public function ajaxComplete() {

		$filter = filter_input(INPUT_POST, 'q', FILTER_SANITIZE_STRING);

		// запрос будем строить опираясь на то, что ввели в поисковой строке (число / строка)
		// если это число, то будем искать по табельному номеру, иначе по совпадению ФИО
		$data = $this->model->getUsers($filter);
		echo json_encode($data);
	}

	public function actionCheck() {

		/*
		$data = filter_input_array(INPUT_POST, [
			'tabel' => FILTER_VALIDATE_INT,
			'password' => FILTER_SANITIZE_STRING,
		]);
		*/

		$data = [
			'login' => 'цтаи',
			'password' => 123,
		];

		$headers = array("X-Requested-With: XMLHttpRequest");
		//$hndl = tmpfile();
		//$md = stream_get_meta_data($hndl);
		//$cookie = get_param($md, 'uri');
		//var_dump($cookie);
		//fclose($hndl);

		$instance = curl_init();
		curl_setopt_array($instance, [
			CURLOPT_TIMEOUT => 5,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTPHEADER => $headers,
			//CURLOPT_COOKIEJAR => $cookie,
			//CURLOPT_COOKIEFILE => $cookie,
			CURLOPT_COOKIESESSION => true,
			CURLOPT_URL => 'http://bid-journal.ru/auth/login/',
			CURLOPT_USERAGENT => get_param($_SERVER, 'HTTP_USER_AGENT'),
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $data,
		]);

		$response = curl_exec($instance);
		//var_dump(curl_error($instance));
		//echo ($response);
		curl_close($instance);

		//unlink($cookie);

		$this->redirect("http://bid-journal.ru/?PHPSESSID=$response");
	}
}