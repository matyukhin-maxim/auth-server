<?php

/**
 * Created by PhpStorm.
 * User: Матюхин_МП
 * Date: 12.05.2016
 * Time: 15:32
 */
class SitesModel extends CModel {

	public function getGrantedSites($uid) {

		// Узнаем какие сайты заблокированы для пользователя
		$block = $this->select('SELECT ifnull(deny, 0) deny FROM person_grant WHERE person_id = :uid', ['uid' => $uid]);
		$row = get_param($block, 0);
		$deny = intval(get_param($row, 'deny', 0));

		// А какие доступны
		$data = $this->select('
			SELECT bit_or(g.access) access
			FROM person_group a
			LEFT JOIN groups g ON a.group_id = g.id
			WHERE a.person_id = :uid
				AND a.deleted = 0', ['uid' => $uid]);

		// Считаем права  (access - deny)
		$row = get_param($data, 0);
		$access = get_param($row, 'access', 0) & (~$deny);

		// и делаем выборку сайтов
		return $this->select('
			SELECT sitename, link
			FROM sites
			WHERE (1 << sitekey) & :access > 0
				AND deleted = 0', ['access' => $access]);
	}
}