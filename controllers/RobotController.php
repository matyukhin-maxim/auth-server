<?php

/**
 * Created by PhpStorm.
 * User: fellix
 * Date: 24.06.16
 * Time: 19:06
 * 
 */
class RobotController extends CController {
	
	private $dot;

	public function actionIndex() {

		$this->render('', false);

		$this->dot = new CSQLServer(Configuration::$connection);

		$this->dot->queryString = 'select * from personal where id in (:list) and deleted = :mark';
		$res = $this->dot->selectOne(null, [
			'list' => [80001681, 80001511, null],
			'mark' => 0,
			'misc' => ['fuck', 'you', 'beach'],
		]);

		var_dump($res);
		var_dump($this->dot);

		$this->render('');
	}
}