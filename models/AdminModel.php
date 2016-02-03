<?php

/**
 * Created by PhpStorm.
 * User: Матюхин_МП
 * Date: 02.02.2016
 * Time: 10:16
 */
class AdminModel extends CModel {

	public function getGroups() {

		$data = $this->select('
		SELECT g.id, g.groupname, count(pg.person_id) cnt
		FROM groups g
		LEFT JOIN person_group pg ON pg.group_id = g.id AND pg.deleted = 0
		WHERE g.deleted = 0
		GROUP BY 1, 2');

		return $data;
	}

	public function saveGroup($group, $gid = null) {

		$this->select('REPLACE INTO groups (id, groupname) VALUES (:gid, :gname)', [
			'gid' => $gid,
			'gname' => $group,
		]);
	}
}