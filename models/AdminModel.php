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
		SELECT g.id, g.groupname, g.access, count(pg.person_id) cnt
		FROM groups g
		LEFT JOIN person_group pg ON pg.group_id = g.id AND pg.deleted = 0
		WHERE g.deleted = 0
		GROUP BY 1, 2, 3');

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

		$data = $this->select('SELECT id, groupname, access FROM groups WHERE id = :gid AND deleted = 0', ['gid' => $group_id]);
		return get_param($data, 0);
	}

	public function accessoryGroup($user_id, $group_id, $delete = 0) {

		$this->select('REPLACE INTO person_group (person_id, group_id, deleted) VALUES (:uid, :gid, :mark)', [
			'uid' => $user_id,
			'gid' => $group_id,
			'mark' => $delete,
		]);

		$response = $this->select('SELECT fullname FROM personal WHERE id = :uid', ['uid' => $user_id]);
		return get_param($response, 0);
	}

	public function getSiteList() {

		return $this->select('SELECT link, sitename, sitekey FROM sites WHERE deleted = 0');
	}

	public function updateGroupAccess($group, $access) {

		$cnt = 0;
		$this->select('UPDATE groups SET access = :mask WHERE id = :gid', [
			'mask' => $access,
			'gid' => $group,
		], $cnt);

		return $cnt === 1;
	}

	public function getUserInformation($uid) {

		$data = $this->select('
			SELECT p.fullname, a.deny, a.deleted, a.pwdhash pw
			FROM personal p
			LEFT JOIN person_grant a ON a.person_id = p.id
			WHERE p.id = :uid', ['uid' => $uid]);

		if (!$data) return false;

		$data = get_param($data, 0);
		$groups = $this->select('
			SELECT p.group_id, g.groupname, g.access
			FROM person_group p
			LEFT JOIN groups g ON g.id = p.group_id
			WHERE p.deleted = 0
				AND p.person_id = :uid
				AND g.deleted = 0', ['uid' => $uid]);

		$data['groups'] = array_column($groups, 'groupname', 'group_id');

		// Считаем доступ к сайтам
		$access = 0;
		foreach ($groups as $item) $access |= (int)get_param($item, 'access');

		$data['grants'] = $access;

		//$deny = (int) get_param($data, 'deny');
		//$data['access'] = $access & (~$deny);
		return $data;
	}

	public function setUserGrants($uid, $password, $deny, $block) {

		$cnt = 0;
		$this->select('REPLACE INTO person_grant (person_id, pwdhash, deny, deleted) VALUES (:pid, :pass, :deny, :block)', [
			'pid' => $uid,
			'pass' => $password,
			'deny' => $deny,
			'block' => $block,
		], $cnt);

		return $cnt > 0;
	}
}