<?php

/**
 * Created by PhpStorm.
 * User: Матюхин_МП
 * Date: 01.02.2016
 * Time: 11:34
 */

/** @property AdminModel $model*/
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

	public function actionGroupList() {

		$groups = $this->model->getGroups();

		foreach ($groups as $item) {
			$id = get_param($item, 'id', '#');
			$this->data['grouplist'] .= CHtml::createTag('a', [
				'class' => 'list-group-item',
				'href' => "/admin/group/$id/",
			], [
				CHtml::createTag('em', ['class' => 'pull-right'], get_param($item, 'cnt', 0) . ' сотрудник (ов)'),
				get_param($item, 'groupname', '?'),
			]);
		}

		$this->render('contents', false);
		$this->render('group-list');
	}

	public function actionSites() {

		$this->render('');
	}

	public function actionGrants() {

		$this->render('');
	}

	public function actionUser() {

		$this->render('', false);
		var_dump($this->arguments);
		$this->render('');
	}

	public function actionGroup() {

		$this->render('', false);
		var_dump($this->arguments);
		$this->render('');
	}

	public function ajaxFilter() {

		$lmodel = new LoginModel();
		$filter = filter_input(INPUT_POST, 'q', FILTER_SANITIZE_STRING);

		$data = $lmodel->getUsers($filter, 21);
		if (count($data) == 0)
			echo CHtml::createTag('div', ['class' => 'alert alert-warning strong'], 'Не найдено');
		else {
			foreach ($data as $person) {
				$link = sprintf('/admin/user/%s/', get_param($person, 'value', 0));
				echo CHtml::createTag('div', ['class' => 'col-md-4 col-sm-6'],
					CHtml::createLink(get_param($person, 'label'), $link, [
						'class' => 'btn btn-default btn-block',
					]));
			}
		}
	}

	public function ajaxGroupAdd() {

		$group = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
		if (!empty($group)) $this->model->saveGroup($group);

		echo CModel::getErrorList();
	}
}