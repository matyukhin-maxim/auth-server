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

	public function saveGroup($group, $gid = null, $delete = 0) {

		$this->select('REPLACE INTO groups (id, groupname, deleted) VALUES (:gid, :gname, :mark)', [
			'gid' => $gid,
			'gname' => $group,
			'mark' => $delete,
		]);

		return $this->getDB()->lastInsertId();
	}

	public function getGroupUsers($group_id) {

		return $this->select('
		SELECT
		  g.group_id,
		  p.fullname,
		  p.id pid
		FROM person_group g
		  LEFT JOIN personal p ON g.person_id = p.id AND p.deleted = 0
		WHERE g.group_id = :gid
			AND g.deleted = 0', ['gid' => $group_id]);
	}

	public function getGroupName($group_id) {

		$data = $this->select('SELECT id, groupname FROM groups WHERE id = :gid AND deleted = 0', ['gid' => $group_id]);
		return get_param($data, 0);
	}

	public function accessoryGroup($user_id, $group_id, $delete = 0) {

		$this->select('REPLACE INTO person_group (person_id, group_id, deleted) VALUES (:uid, :gid, :mark)', [
			'uid' => $user_id,
			'gid' => $group_id,
			'mark' => $delete,
		]);

		$response = $this->select('select fullname from personal where id = :uid', ['uid' => $user_id]);
		return get_param($response, 0);
	}
}