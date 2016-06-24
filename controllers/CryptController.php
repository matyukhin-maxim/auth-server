<?php

/**
 * Created by PhpStorm.
 * User: �������_��
 * Date: 27.05.2016
 * Time: 13:00
 *
 * @property SyncModel $model
 */
class CryptController extends CController {

	public $text;
	public $letters;
	public $len;

	public function __construct() {
		parent::__construct();

		$this->text = '
		����� � ��� ��� ������.
		� ������� �� �������������
		��������� �� �����. � ��� �����
		������ ��������, �������. �����
		��, ������, ������, � �� ������
		�������� � ��������, ��� � ���������
		����������� �� ���-��. �� � �� ����,
		��� �� � �������� ��������. �����
		��� � ������� �������� �������������
		� ������ �������� ������, �����
		���������� ������ ������������ � ���������
		� ����������. � ���� ��� �������, ���
		�� ��������� � ������� � ������ ���������
		� ������ �����. ';

		//$text = '
		//��Ȩ� �ͨ�� ����� ����� ����� ����� ����� ����� ����� ����� ɨ��� �����
		//����� ����� ����� ����� ����� ����� ����� ����� ��ͨ� ����� ����� �����
		//����� ����� ����� ����� ����� ����� Ш��� ����� ����� �ɨ�� �ب�� ���Ȩ
		//����� ����� ����� ����� ����� ����� ����� ����� ����� ����� Ө��� �����
		//����� ����� ����� ����� ����� ����� ����� ����� ��Ш� ����� ����� �����
		//����� ����� ����� ����� ����� ��٨� ����� ����� ����� ����� ����� �����
		//����� ����� ����� ���ʨ ����� ����� ����� ���ʨ ����� ����� ����� �����
		//����� ����� ����� ����� ����� ����� ����� ����� ����� ����� ����� �����
		//����� ����� ����� ����� ��ɨ� ����� ����� ����� ��ͨ� ����� ����� �����
		//����� ����� ����� ����� ����� ����� ����� ����� ����� ����� ����� �����
		//����� ����� ����� ����� ����� ����� ����� ����� ����� ����� ����� �����
		//����� ����� ����� ����� ��Ш� ����� ʨ��� ����� ����� ����� Ш��� �����
		//����� ����� Ȩ��� ����� ����� ����� ����� ����� ����� ����� ����� �����
		//����� ����� ����� ����� ���';

		//$text = '���� ���� ������ ������ ������� � ����� �����';
		//$text = '���� � ����� ����� �������';

		$this->letters = $this->split_all('��������������������������������', 1);
	}

	public function actionIndex() {

		$this->actionCrypt();
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
		for ($idx = 0; $idx < $len; $idx++) $res[$word[$idx]]++;

		return $res;
	}

	private function calcIC($someText) {

		$ic = 0.0;
		$len = strlen($someText);
		$freq = $this->cnt_letters($someText);
		foreach ($freq as $cnt) $ic += ($cnt * ($cnt - 1)) / ($len * ($len - 1));

		return $ic; //$ic / ($len * ($len - 1));
	}

	private function makeCesar($line, $delta) {
		$letters = join('', $this->letters);
		$len = strlen($letters);

		for ($idx = 0; $idx < strlen($line); $idx++) {
			$pos = strpos($letters, $line[$idx]);
			if ($pos === false) return '!'; // ����� � ������ �� � ��������
			$pos = ($pos + $delta + $len) % $len;

			$line[$idx] = $letters[$pos];
		}

		return $line;
	}

	/**
	 * @param $a int
	 * @param $b int
	 * @return int
	 */
	private function gcd($a, $b) {
		return $b === 0 ? $a : $this->gcd($b, $a % $b);
	}

	public function methodShift($line) {

		// ����� ����� ����� ������� ������

		$len = strlen($line);

		$guess = [];
		for ($cnt = 1; $cnt < $len; $cnt++) {

			$work = substr($line, $cnt) . substr($line, 0, $cnt);

			$coincidence = 0;
			for ($idx = 0; $idx < $len; $idx++)
				$coincidence += intval($line[$idx] === $work[$idx]);

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

		return $key;
	}

	public function methodKasisky($line) {

		// ���������� ����� ���� ������� �������

		$digLen = 3;
		$repCount = [];
		$len = strlen($line);

		for ($i = 0; $i < $len - $digLen + 1; $i++) {
			$sub = substr($line, $i, $digLen);
			for ($j = $i + 1; $j < $len - $digLen + 1; $j++) {
				if (substr($line, $j, $digLen) === $sub)
					$repCount[] = $j - $i;
			}
		}

		$nods = [];
		for ($i = 0; $i < count($repCount); ++$i)
			for ($j = $i + 1; $j < count($repCount); ++$j)
				$nods[$this->gcd($repCount[$i], $repCount[$j])]++;
		arsort($nods);
		var_dump($nods);
		$key = array_keys($nods)[0];

		return $key;
	}

	public function actionCrypt() {
		header('Content-Type: text/html; charset=cp1251');

		$text = preg_replace('/[^�-��-���]/', '', $this->text);
		$text = strtolower($text);
		$len = strlen($text);



		//var_dump($text);
		var_dump($this->calcIC($text));
		echo CHtml::createTag('hr');


		//$keyLength = $this->methodKasisky($text);
		$keyLength = 13;

		// ��������� ����� �� ����� ��� ������
		$K = [];
		for ($idx = 0; $idx < $len; $idx++) $K[$idx % $keyLength] .= $text[$idx];

		//var_dump($this->letters);

		//$text = str_repeat('�', $len);

		$bestIC = 0.0;
		$remember = $text;
		$key = [];
		//$key = explode(' ', '� � � � � � � � � � � � �');

		foreach ($K as $pos => $part) {
			var_dump($text);

			// ������ ����� ������� ������� �� ���� �����,
			// � ���� ������ ������ ���������� �� ����� ������

			//$bestIC = 0.0;
			$letter = '�';

			for ($cnt = 0; $cnt < count($this->letters); $cnt++) {
				$work = $this->makeCesar($part, -$cnt);

				// ���������� ����� ������� � ������������ �����
				for ($cpos = 0; $cpos < strlen($work); $cpos++)
					$text[$cpos * $keyLength + $pos] = $work[$cpos];

				$curIC = $this->calcIC($text);
				//if ($key[$pos] === $this->letters[$cnt]) var_dump($curIC);

				//var_dump([
				//	$cnt,
				//	$work,
				//	$curIC,
				//	$bestIC,
				//	$this->letters[$cnt],
				//	substr($text, 0, 30),
				//]);

				//if (abs($curIC - $bestIC) < 1e-3) {
				if ($curIC > $bestIC) {

					$bestIC = $curIC;
					$letter = $this->letters[$cnt];
					//echo CHtml::createTag('hr');
					$remember = $text;
				}
			}
			var_dump([
				$letter,
				$bestIC,
			]);
			echo CHtml::createTag('hr');

			// ����� � ������� ��������� ������ IC ����������
			$key[] = $letter;
			$text = $remember;

			//break;
		}

		//var_dump($bestIC);
		var_dump(join(' ', $key));

	}
}