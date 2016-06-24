<?php

/**
 * Created by PhpStorm.
 * User: fellix
 * Date: 28.01.16
 * Time: 20:51
 */

/** @property SitesModel $model */
class SitesController extends CController {

	public function actionIndex() {

		$this->render('', false);

		$uid = get_param($this->authdata, 'id');

		// Если не авторизованы то идет лесом
		if (!$uid) $this->redirect('/login/');


		// иначе получим список доступных ему сайтов, и нарисуем ссылки на них
		$sites = $this->model->getGrantedSites($uid);

		$this->render('', false);
		if (count($sites) === 0) {

			$this->render('no-sites');
			return;
		}

		$this->data['siteList'] = '';

		$secure = [
			$uid,
			date('Y-m-d H:i:s'),
			//makeSortName(get_param($this->authdata, 'fullname')),
			get_param($this->authdata, 'fullname'),
		];

		foreach ($sites as $item) {

			$link = get_param($item, 'link');
			$name = get_param($item, 'sitename');
			$key = get_param($item, 'passkey');

			$cipherText = Cipher::encode($secure, $key, true);
			$cipherText = strtr($cipherText, '+/=', '-,_');
			$link .= "auth/openid/";
			if (strpos($link, 'oper') !== false) $link .= 'token/';

			$link .= $cipherText;

			$this->data['siteList'] .= CHtml::createLink($name, null, [
				'href' => $link,
				'class' => 'list-group-item strong italic',
			]);
		}

		$this->render('panel', false);
		$this->render('');
	}
}