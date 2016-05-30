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

		//$this->sql = new PDO('odbc:Driver={SQL Server};Server=dgk10srv086;Database=PORTAL; Uid=portal;Pwd=12345678',
		//	'', '', [
		//		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		//		PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
		//		//PDO::ATTR_EMULATE_PREPARES => false,
		//		//PDO::ATTR_STRINGIFY_FETCHES => false,
		//	]);
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

	private function split_all($input, $length, $count = -1) {

		$res = [];
		if ($length < 1) return $res;

		while (strlen($input) && $count-- !== 0) {

			$sub = substr($input, 0, $length);
			if (strlen($sub) === $length) $res[] = $sub;
			$input = substr($input, $length);
		}
		return $res;
	}

	private function cnt_letters($word) {

		$res = [];
		$len = strlen($word);
		for ($idx = 0; $idx < $len; $idx++) $res[ $word[$idx] ]++;

		return $res;
	}

	public function actionCrypt() {
		header('Content-Type: text/html; charset=cp1251');
		//$this->render('', false);

		//$text = 'шмхти — яьж оеш мнвтре. ' .
		//	'х вуйтиыз см уюйыфэтижннчн ' .
		//	'тейэхтфто то бъцьп. о юур ртьлн ' .
		//	'вихпхё юхзятхну, хэшюыэи. ртьлн ' .
		//	'рр, бъмсэи, оеъцды, м ые чрхктс ' .
		//	'эчбафьчс — уопьтцуз, ддш н ъяяхёджсд ' .
		//	'аьахмеэидчй ря иць-чы. юэ ё цу хцяп, ' .
		//	'ыау ял — эщпнецъы ёьихгута. лтфяп ' .
		//	'цтёс в вшуйгчл бтшуэпбг ёухкъйиытывбп' .
		//	' м ёиъсвт ънъяржцб яштгоц, рксйл ' .
		//	'сйючбятячх фэчтзц шпцтсушхржцб я фдфоьцямм' .
		//	' п тфаслълъущ. щ ушпн аий гпбьхгаё, ычь ' .
		//	'цд ууцэсикшг ж эчбафьч — нлащфу пнпячабюп' .
		//	' р фчлычд вчяры. ';

		$text = 'оаит ббнп хяпмыб юмазчб фряачма т гушкд яншиы';

		$text = mb_ereg_replace('[^А-Яа-яЁё]', '', $text);
		$len = mb_strlen($text);

		$text = mb_convert_encoding($text, 'cp1251');

		// метод сдвига
		$guess = [];
		for ($cnt = 1; $cnt < $len; $cnt++) {

			$work = substr($text, $cnt) . substr($text, 0, $cnt);

			$coincidence = 0;
			for ($idx = 0; $idx < $len; $idx++)
				$coincidence += intval($text[$idx] === $work[$idx]);

			$guess[$cnt] = $coincidence;
			//var_dump([
			//	'A' => $text,
			//	'B' => $work,
			//	'C' => $cnt,
			//	'D' => $coincidence,
			//]);
		}

		//$key = array_keys($guess, max($guess))[0];
		$key = 5;
		//var_dump($key);

		//arsort($guess);
		//var_dump($guess);

		// индекс совпадений
		var_dump($text);
		for ($cnt = 2; $cnt < 10; $cnt++) {
			$work = '';
			for ($idx = 0; $idx < $len; $idx++)
				if ($idx % $cnt === 0) $work .= $text[$idx];

			$freq = $this->cnt_letters($work);
			var_dump([
				'L' => $cnt,
				'W' => $work,
				'F' => join(' ', array_values($freq)),
			]);
		}

		$K = [];
		for ($idx = 0; $idx < $len; $idx++) $K[$idx % $key] .= $text[$idx];

		//var_dump($K);

		//$this->render('');
	}

	public function getDescription() {

		return [
			'actionIndex' => 'Эта страница',
			'actionSyncUsers' => 'Загрузить пользователей',
			'actionSyncPhones' => 'Загрузить телефоны',
			'actionSyncPhotos' => 'Загрузить фотографии',
			'actionCrypt' => 'Издевательство',
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