<?php

/**
 * Created by PhpStorm.
 * User: Матюхин_МП
 * Date: 19.05.2016
 * Time: 14:18
 */

/**
 * @property PDO $con
 */
class ReportController extends CController {

	private $con;

	public function __construct() {
		parent::__construct();

		try {

			$this->con = new PDO('odbc:Driver={SQL Server};Server=dgk90tch006;Database=DevNet; Uid=sa;Pwd=Asu31281080',
				'', '', [
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
					//PDO::ATTR_EMULATE_PREPARES => false,
					//PDO::ATTR_STRINGIFY_FETCHES => false,
				]);

		} catch (Exception $ex) {

			$msg = $ex->getMessage();
			charsetChange($msg);
			throw new Exception($msg);
		}
	}

	public function actionDraw($repData) {

		$text = '';
		foreach ($repData as $row) {

			$text .= CHtml::createTag('tr', null, [
				CHtml::createTag('td', null, get_param($row, 'line')),
				CHtml::createTag('td', null, get_param($row, 'start')),
				CHtml::createTag('td', null, get_param($row, 'stop')),
				CHtml::createTag('td', ['class' => 'text-right'], number_format(get_param($row, 'result'), 3, ',','')),
				CHtml::createTag('td', null, get_param($row, 'working')),
			]);
		}

		if (count($repData)) {

			$totalWeight = array_sum(array_column($repData, 'result'));
			$totalWork = array_sum(array_column($repData, 'sec'));

			$text .= CHtml::createTag('tr', ['class' => 'success strong'], [
				CHtml::createTag('td', ['class' => 'text-right', 'colspan' => 3], 'Итоги:'),
				CHtml::createTag('td', ['class' => 'text-right'], number_format($totalWeight, 3, ',', '')),
				CHtml::createTag('td', null, sprintf("%.2f минут", $totalWork / 60)),
			]);
		}

		return $text;
	}

	public function actionIndex() {

		$this->scripts[] = 'report';

		$this->render('form', false);
		$this->render('table');
	}

	public function ajaxQuery() {

		$params = filter_input_array(INPUT_POST, [
			'bdate' => FILTER_SANITIZE_STRING,
			'edate' => FILTER_SANITIZE_STRING,
			'lineA' => FILTER_VALIDATE_INT,
			'lineB' => FILTER_VALIDATE_INT,
		]);

		$params['edate'] .= ' 23:59:59';

		if (get_param($params, 'lineA')) {

			$rep = $this->actionGetData(1, get_param($params, 'bdate'), get_param($params, 'edate'));
			echo $this->actionDraw($rep);
		}

		if (get_param($params, 'lineB')) {

			$rep = $this->actionGetData(2, get_param($params, 'bdate'), get_param($params, 'edate'));
			echo $this->actionDraw($rep);
		}
	}

	public function actionGetData($needle = 1, $dt_begin = null, $dt_end = null) {

		$dt_begin = $dt_begin ?: date('Y-m-d');
		$dt_end   = $dt_end   ?: '2016-12-31';

		$st = $this->con->prepare("
			SELECT l.[key-bit_flags] type, l.timepoint, w.[value]
			FROM log l
			LEFT JOIN value_weight w ON w.[key-log] = l.[key]
			WHERE l.line = :line
				AND l.[key-bit_flags] IN (45,44)
				AND l.timepoint BETWEEN :bdate AND :edate
			ORDER BY l.timepoint");

		$st->execute([
			'line' => $needle,
			'bdate' => $dt_begin,
			'edate' => $dt_end,
		]);

		$data = $st->fetchAll();
		//array_walk_recursive($data, 'charsetChange');

		$sd = $sw = 0;
		$report = array(); $cnt = 0;
		$line = mb_substr("-АБВГД", $needle, 1);

		foreach ($data as $row) {

			//var_dump($row);

			$weight = floatval($row['value']);
			if ($row['type'] === '45') {

				$sd = $row['timepoint'];
				$sw = $weight;
			} else {

				if ($sd === 0) continue;

				// current measures
				$cw = floatval($row['value']);
				$cd = $row['timepoint'];

				//if ($cw - $sw < -500) var_dump("!!!!!!");

				$dtb = DateTime::createFromFormat('Y-m-d H:i:s.u', $sd);
				$dte = DateTime::createFromFormat('Y-m-d H:i:s.u', $cd);
				$work = $dtb->diff($dte);

				$cnt++;
				$report[$cnt]['start'] = $dtb->format('d.m.Y H:i:s');
				$report[$cnt]['stop'] = $dte->format('d.m.Y H:i:s');
				$report[$cnt]['b_weight'] = $sw;
				$report[$cnt]['e_weight'] = $cw;
				$report[$cnt]['result'] = abs($cw - $sw);
				$report[$cnt]['working'] = $work->format('%H:%I:%S');
				$report[$cnt]['sec'] = abs($dtb->getTimestamp() - $dte->getTimestamp());
				$report[$cnt]['line'] = $line;

				// flush vars
				$sd = $sw = 0;
			}
		}

		return $report;
	}
}