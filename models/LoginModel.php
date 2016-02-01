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
        FROM personal
        WHERE $field LIKE :filter
              AND deleted = 0
        ORDER BY fullname
        $limit
        ", ['filter' => $filter]);

		return $result;
	}
}