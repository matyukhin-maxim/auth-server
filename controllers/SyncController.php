<?php

/**
 * Created by PhpStorm.
 * User: Матюхин_МП
 * Date: 27.05.2016
 * Time: 13:00
 *
 * @property SyncModel $model
 */
class SyncController extends CController {

	private $sql = null;

	public function __construct() {
		parent::__construct();

		$this->sql = new PDO('odbc:Driver={SQL Server};Server=dgk10srv086;Database=PORTAL; Uid=portal;Pwd=12345678',
			'', '', [
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
				//PDO::ATTR_EMULATE_PREPARES => false,
				//PDO::ATTR_STRINGIFY_FETCHES => false,
			]);
	}

	public function actionIndex() {

		$methods = get_class_methods(__CLASS__);
		$this->render('', false);

		$buttons = '';
		$descr = $this->getDescription();

		foreach ($methods as $link) {

			if (preg_match('/^action/', $link)) {

				$buttons .= CHtml::createLink(get_param($descr, $link, $link),
					mb_strtolower($this->createActionUrl(mb_substr($link, 6))), [
						'class' => 'btn btn-default btn-block strong btn-lg',
					]);
			}
		}

		echo CHtml::createTag('div', ['class' => 'panel panel-body'], $buttons);

		$this->render('');
	}

	public function actionSyncUsers() {

		if (!$this->sql) $this->redirect(['back' => 1]);

		// один фильтр чтобы отобрать ГРЭСовских чувачков
		$arguments = ['fmt' => '8000%'];

		$sPerson = $this->sql->prepare('
			SELECT
				p.fio, p.tabnomer, p.dr, p.dolgnost, p.email
			FROM pers p
			WHERE p.tabnomer LIKE :fmt
			ORDER BY p.fio
		');

		$sPhone = $this->sql->prepare('
			SELECT p.tabnomer, t.tel
			FROM tel t
			LEFT JOIN pers p ON t.id_pers = p.id
			WHERE p.tabnomer LIKE :fmt
		');


		$sPhone->execute($arguments);
		$phones = $sPhone->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_COLUMN);
		$persons = $this->prepareData($sPerson, $arguments);


		foreach ($phones as $person => $numbers) $phones[$person] = join(' / ', $numbers);
		$cnt = count($persons);

		$this->model->startTransaction();
		$this->model->markAllUsersAsDeleted();

		$ok = true;
		for ($idx = 0; $idx < $cnt; $idx++) {
			$person = $persons[$idx];

			$person['phones'] = get_param($phones, get_param($person, 'tabnomer'), null);
			$ok &= $this->model->updateUser($person);
		}

		$this->model->stopTransaction($ok);
		$this->preparePopup($this->model->getErrors());

		if ($ok) $this->preparePopup('Пользователи синхронизированы', 'alert-success');

		$this->redirect('/sync/');
	}

	public function actionSyncPhones() {

		if (!$this->sql) $this->redirect(['back' => 1]);

		$this->render('', false);
		$sPhone = $this->sql->prepare('
			SELECT p.tabnomer, t.tel
			FROM tel t
			LEFT JOIN pers p ON t.id_pers = p.id
			WHERE p.tabnomer LIKE :fmt
		');

		$sPhone->execute(['fmt' => '8000%']);
		$sync = $sPhone->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_COLUMN);

		var_dump($sync);

		$this->render('');
	}

	public function actionSyncPhotos() {

		if (!$this->sql) $this->redirect(['back' => 1]);


		$this->render('');
	}

	public function getDescription() {

		return [
			'actionIndex' => 'Эта страница',
			'actionSyncUsers' => 'Загрузить пользователей',
			'actionSyncPhones' => 'Загрузить телефоны',
			'actionSyncPhotos' => 'Загрузить фотографии',
		];
	}

	/**
	 * @param $statement PDOStatement
	 * @param null $params
	 * @return array
	 */
	private function prepareData(&$statement, $params = null) {

		$statement->execute($params);

		$errors = [$statement->errorInfo(), $this->sql->errorInfo()];
		array_walk_recursive($errors, 'charsetChange');
		//var_dump($errors);

		$result = $statement->fetchAll();
		array_walk_recursive($result, 'charsetChange');

		return $result;
	}
}