<?php

/**
 * Created by PhpStorm.
 * User: fellix
 * Date: 28.01.16
 * Time: 20:52
 */

/** @property LoginModel $model*/
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
}