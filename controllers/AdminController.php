<?php

/**
 * Created by PhpStorm.
 * User: Матюхин_МП
 * Date: 01.02.2016
 * Time: 11:34
 */
class AdminController extends CController {

	public function __construct() {
		parent::__construct();

		$this->scripts[] = 'administration';
	}

	public function actionIndex() {

		$this->actionUserList();
	}

	public function actionUserList() {

		$this->render('contents', false);

		$this->render('user-list', false);
		$this->render('');
	}

	public function actionGroupEditor() {

		$this->render('');
	}

	public function actionSites() {

		$this->render('');
	}

	public function actionGrants() {

		$this->render('');
	}

	public function ajaxFilter() {

		$lmodel = new LoginModel();
		$filter = filter_input(INPUT_POST, 'q', FILTER_SANITIZE_STRING);

		$cnt = 0;
		$buffer = '';
		$data = $lmodel->getUsers($filter, 12);
		foreach ($data as $person) {
			$cnt++;

			$this->data['fullname'] = get_param($person, 'label');
			$buffer .= $this->renderPartial('user-panel');
			if ($cnt % 3 == 0)  {
				echo CHtml::createTag('div', ['class' => 'row'], $buffer);
				$buffer = '';
			}
		}
		if ($buffer) echo CHtml::createTag('div', ['class' => 'row'], $buffer);
	}
}