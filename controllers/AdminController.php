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

		$this->render('contents', false);
		$this->scripts[] = 'user-manage';
		$this->title = 'Редактор пользователя';

		$uid = filter_var(get_param($this->arguments, 0), FILTER_VALIDATE_INT, [
			'options' => ['min_range' => 1,	'default' => 0]
		]);


		$userInfo = $this->model->getUserInformation($uid);
		if (!$userInfo) {
			$this->preparePopup('Информация о сотруднике не найдена');
			$this->render('');
			return;
		}

		$this->data['username'] = get_param($userInfo, 'fullname');

		$options = '';
		$groups = $this->model->getGroups();
		$memberOf = $userInfo['groups'];
		foreach ($groups as $group) {

			$gid = get_param($group, 'id');
			$options .= CHtml::createTag('li', [
				'class' => 'list-group-item btn group' . (array_key_exists($gid, $memberOf) ? ' active' : ''),
				'data-access' => get_param($group, 'access'),
			], get_param($group, 'groupname'));
		}

		$this->data['tabNumber'] = $uid;
		$this->data['userGroups'] = $options;

		$deny = intval(get_param($userInfo, 'deny'));
		$block = get_param($userInfo, 'deleted');
		$block = is_null($block) ? 1 : $block;

		$this->data['buttonBlock'] = CHtml::createTag('a', [
			'class' => 'btn btn-default strong',
			'href'  => $block ? "/admin/unblockUser/$uid/" : "/admin/blockUser/$uid/",
		], $block ? 'Разблокировать' : 'Заблокировать');

		$sites = $this->model->getSiteList();
		foreach ($sites as $link) {

			$class = 'site col-xs-3 btn';
			$siteKey = intval(get_param($link, 'sitekey'));
			if ((1 << $siteKey) & $deny) $class .= ' block';

			$this->data['siteList'] .= CHtml::createButton(get_param($link, 'sitename'), [
				'class' => $class,
				'data-key' => $siteKey,
			]);
		}

		//var_dump($userInfo);

		$this->render('user-control', false);
		$this->render('');
	}

	public function actionGroup() {

		$this->render('contents', false);
		$this->scripts[] = 'admin-group';

		$group_id = filter_var(get_param($this->arguments, 0), FILTER_VALIDATE_INT, [
			'options' => ['min_range' => 1,	'default' => 0]
		]);

		// Название группы прочитаем
		$group = $this->model->getGroupName($group_id);
		if (empty($group)) {
			throw new Exception("Запрошенная группа не найдена");
		}

		if (get_param($this->arguments, 1) === 'delete') {
			$this->model->saveGroup(get_param($group, 'groupname', '?'), $group_id, 1);
			if (count($this->model->getErrors())) {
				$this->preparePopup($this->model->getErrorList());
			} else	$this->preparePopup('Группа удалена', 'alert-success');

			$this->redirect('/admin/grouplist/');
			return;
		}

		$this->data['group_name'] = get_param($group, 'groupname', '?');
		$this->data['group_id'] = $group_id;

		// Получим список пользователей, которые привязаны к этой группе
		$ulist = $this->model->getGroupUsers($group_id);
		foreach ($ulist as $person) {

			$this->data['plist'] .= CHtml::createTag('li', ['class' => 'list-group-item group-user'],[
				CHtml::createLink('&times;', '#', [
					'class' => 'close deluser',
					'data-group' => $group_id,
					'data-user' => get_param($person, 'pid'),
				]),
				get_param($person, 'fullname'),
			]);
		}

		$access = (int) get_param($group, 'access', []);
		$sites = $this->model->getSiteList();
		foreach ($sites as $link) {

			$key = intval(get_param($link, 'sitekey'));
			$key = $key > 32 ? 32 : $key;
			$granted = ($access & (1 << $key)) > 0;
			$this->data['siteList'] .= CHtml::createTag('li', [
				'class' => 'list-group-item site-link ' . ($granted ? 'btn-success' : ''),
				'data-key' => $key,
			], get_param($link, 'sitename'));
		}

		$this->render('group-manage', false);
		$this->render('');
	}

	public function actionChangeGroup() {

		$gname      = filter_input(INPUT_POST, 'group-name', FILTER_SANITIZE_STRING);
		$group_id   = filter_input(INPUT_POST, 'group-id',   FILTER_VALIDATE_INT);

		if ($gname && $group_id) {
			$this->model->saveGroup($gname, $group_id);
			$elist = CModel::getErrorList();
			if ($elist) {
				$this->preparePopup($elist);
			} else $this->preparePopup('Название групы сохранено.', 'alert-success');
		}

		$this->redirect(['back' => 1]);
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
						'class' => 'btn btn-default btn-lg btn-block',
					]));
			}
		}
	}

	public function ajaxGroupAdd() {

		$group = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);

		if (empty($group)) return;

		$group_id = $this->model->saveGroup($group);

		if (count($this->model->getErrors())) {
			$this->preparePopup($this->model->getErrorList());
		} else {
			$this->preparePopup('Группа успешно создана', 'alert-success');
			echo CHtml::createTag('a', [
				'class' => 'list-group-item',
				'href' => "/admin/group/$group_id/",
			], [
				CHtml::createTag('em', ['class' => 'pull-right'], '0 сотрудник (ов)'),
				$group,
			]);
		}
	}

	public function ajaxSelection() {

		// Почти тоже самое что и при фильтрации пользователей,
		// но результаты будем рисовать немного по другому (с чекбоксами)

		$lmodel = new LoginModel();
		$filter = filter_input(INPUT_POST, 'q', FILTER_SANITIZE_STRING);

		$data = $lmodel->getUsers($filter);
		if (count($data) == 0)
			echo CHtml::createTag('div', ['class' => 'alert alert-warning strong'], 'Не найдено');
		else {
			foreach ($data as $person) {

				echo CHtml::createTag('div', ['class' => 'col-sm-6'],
					CHtml::createButton(get_param($person, 'label'),  [
						'class' => 'btn btn-default btn-block btn-select role-name',
						'data-user' => get_param($person, 'value'),
						'title' => get_param($person, 'label'),
					]));
			}
		}
	}

	public function ajaxAccessoryUser() {

		$data = filter_input_array(INPUT_POST, [
			'user'  => FILTER_VALIDATE_INT,
			'group' => [
				'filter' => FILTER_VALIDATE_INT,
				'options' => ['min_range' => 1],
			],
			'remove' => [
				'filter' => FILTER_VALIDATE_INT,
				'options' => [
					'min_range' => 0,
					'max_range' => 1,
					'default' => 0,
				],
			],
		], false);

		$uid = get_param($data, 'user');
		$gid = get_param($data, 'group');
		$delete = get_param($data, 'remove', 0);

		if (!($uid && $gid)) {
			$this->preparePopup('Параметры заданы неверно');
			return;
		}

		$person = $this->model->accessoryGroup($uid, $gid, $delete);
		if (count($this->model->getErrors())) {
			$this->preparePopup($this->model->getErrorList());
		} else {
			// Удачно привязали/отвязали пользователя
			if ($delete === 0) {

				echo CHtml::createTag('li', ['class' => 'list-group-item group-user'], [
					CHtml::createLink('&times;', '#', [
						'class' => 'close deluser',
						'data-group' => $gid,
						'data-user' => $uid,
					]),
					get_param($person, 'fullname'),
				]);
			} else echo 'ok';
		}
	}

	public function ajaxGroupAccess() {

		$sites = filter_input(INPUT_POST, 'sites', FILTER_VALIDATE_INT, ['flags' => FILTER_REQUIRE_ARRAY]) ?: [];
		$group = filter_input(INPUT_POST, 'group', FILTER_VALIDATE_INT) ?: -1;

		$access = 0;
		foreach($sites as $key) $access |= (1 << $key);

		$res = $this->model->updateGroupAccess($group, $access);
		$this->preparePopup($this->model->getErrorList());

		if ($res) $this->preparePopup('Информация сохранена', 'alert-success');
	}

	public function actionBlockUser() {

		$uid = filter_var(get_param($this->arguments, 0), FILTER_VALIDATE_INT, [
			'options' => ['min_range' => 1,	'default' => 0]
		]);


		$userInfo = $this->model->getUserInformation($uid);
		if (!$userInfo) {
			$this->preparePopup('Информация о сотруднике не найдена');
			$this->redirect(['back' => 1]);
			return;
		}

		$password = get_param($userInfo, 'pw');
		$deny = get_param($userInfo, 'deny');
		$block = get_param($userInfo. 'deleted');

		$res = $this->model->setUserGrants($uid, $password, $deny, 1);
		if ($res) $this->preparePopup('Пользователь заблокирован', 'alert-warning');
		else $this->preparePopup($this->model->getErrorList());

		$this->redirect(['back' => 1]);
	}

	public function actionUnblockUser() {

		$uid = filter_var(get_param($this->arguments, 0), FILTER_VALIDATE_INT, [
			'options' => ['min_range' => 1,	'default' => 0]
		]);


		$userInfo = $this->model->getUserInformation($uid);
		if (!$userInfo) {
			$this->preparePopup('Информация о сотруднике не найдена');
			$this->redirect(['back' => 1]);
			return;
		}

		$password = get_param($userInfo, 'pw');
		$deny = get_param($userInfo, 'deny');
		$block = get_param($userInfo. 'deleted');

		$res = $this->model->setUserGrants($uid, $password, $deny, 0);
		if ($res) $this->preparePopup('Пользователь разблокирован', 'alert-success');
		else $this->preparePopup($this->model->getErrorList());

		$this->redirect(['back' => 1]);
	}
}