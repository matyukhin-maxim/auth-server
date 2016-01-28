<?php

/**
 * Created by PhpStorm.
 * User: fellix
 * Date: 28.01.16
 * Time: 20:52
 */
class LoginController extends CController {

	public function actionIndex() {

		$this->scripts[] = 'auth';

		Session::del('auth');
		$this->render('form');
	}

	public function ajaxComplete() {

		echo json_encode([
			['fullname' => 'Max', 'tabnumber' => 1681],
			['fullname' => 'Fax', 'tabnumber' => 1423],
			['fullname' => 'Pax', 'tabnumber' => 1754],
		]);
	}
}