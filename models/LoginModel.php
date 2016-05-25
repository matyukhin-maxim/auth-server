<?php

/**
 * Created by PhpStorm.
 * User: Матюхин_МП
 * Date: 29.01.2016
 * Time: 10:20
 */
class LoginModel extends CModel {

	public function getUsers($filter, $limit = 0) {

		if (empty($filter)) return [];
		$field = 'id';

		if (is_numeric($filter)) {
			$filter = "8000$filter%";
		} else {
			$filter .= '%';
			$field = 'fullname';
		}

		$limit = $limit ? sprintf("LIMIT %d ", intval($limit)) : '';
		$result = $this->select("
        SELECT
          id       value,
          fullname label
        FROM openid.personal
        WHERE $field LIKE :filter
              AND deleted = 0
        ORDER BY fullname
        $limit
        ", ['filter' => $filter]);

		return $result;
	}

	public function getPerson($tabnom) {

		$res = $this->select('
 	        SELECT p.id, p.fullname, g.pwdhash, ifnull(g.deny, 0) deny
 	        FROM personal p
			LEFT JOIN person_grant g ON p.id = g.person_id AND g.deleted = 0
			WHERE p.id = :uid AND p.deleted = 0', ['uid' => $tabnom]);
		return get_param($res, 0);
	}

	public function changePassword($uid, $pass) {

		$cnt = 0;
		$this->select('UPDATE person_grant SET pwdhash = :phash WHERE person_id = :uid', [
			'uid' => $uid,
			'phash' => sha1($pass),
		], $cnt);

		return $cnt === 1;
	}
}