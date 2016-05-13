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

		// Запомним адрес откуда пришли (с какого сайта)
		// чтобы после авторизации вернутся на него
		// todo Запомнить HTTP_REFERER


		$this->scripts[] = 'auth';

		Session::del('auth');
		$this->data['authdata'] = false;

		$this->render('form');
	}

	public function ajaxComplete() {

		$filter = filter_input(INPUT_POST, 'q', FILTER_SANITIZE_STRING);

		// запрос будем строить опираясь на то, что ввели в поисковой строке (число / строка)
		// если это число, то будем искать по табельному номеру, иначе по совпадению ФИО
		$data = $this->model->getUsers($filter, 20);
		if (count($data) === 0) $this->preparePopup('Пользователь не найден', 'alert-info');
		echo json_encode($data);
	}

	public function ajaxCheck() {

		$data = filter_input_array(INPUT_POST, [
			'tabel' => FILTER_VALIDATE_INT,
			'password' => FILTER_SANITIZE_STRING,
		]);

		//var_dump($data);
		$person = $this->model->getPerson($data['tabel']);

		// Найденм запрошенного пользователя
		if (!$person) return $this->preparePopup("Пользователь не найден. \nПопробуйте еще разили обратитесь в отдел АСУ.");

		// Сверим пароли
		$upass = get_param($data, 'password');

		// Если он пустой (удален), то доступа нет
		if (!get_param($person, 'pwdhash')) return $this->preparePopup("Выбранный пользоватль заблокирован.");

		// про супер-паоль тоже на забываем...
		if ($upass !== '312810800' && strcmp(sha1($upass), get_param($person, 'pwdhash')))
			return $this->preparePopup("Пароль указан не верно.
				Проверте клавиши <kbd>Caps Lock</kbd> / <kbd>Num Lock</kbd> и повторите попытку");

		unset($person['pwdhash']);
		Session::set('auth', $person);

		echo "OK";
	}

	public function actionExit() {

		Session::del('auth');
		Session::destroy();

		$this->redirect();
	}
}