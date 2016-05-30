<?php

/**
 * Created by PhpStorm.
 * User: Матюхин_МП
 * Date: 27.05.2016
 * Time: 15:40
 */
class SyncModel extends CModel {

	public function updateUser($data) {

		$cnt = 0;
		$this->select('
			REPLACE INTO personal (id, fullname, position, birth, email, phones, deleted)
			VALUES (:tabnomer, :fio, :dolgnost, :dr, :email, :phones, 0)', $data, $cnt);

		return $cnt > 0;
	}

	public function markAllUsersAsDeleted() {

		$this->select('UPDATE personal SET deleted = 1');
	}
}