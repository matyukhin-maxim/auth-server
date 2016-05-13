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

		//$req = curl_init();

		foreach ($sites as $item) {

			$link = get_param($item, 'link');

			$this->data['siteList'] .= CHtml::createTag('form', [
				'action' => $link . 'auth/openid/',
				//'target' => '_blank',
				'method' => 'post',
			], [
				CHtml::createTag('input', ['type' => 'hidden', 'name' => 'uid', 'value' => $uid]),
				CHtml::createTag('input', ['type' => 'hidden', 'name' => 'data', 'value' => get_param($this->authdata, 'fullname')]),
				CHtml::createButton(get_param($item, 'sitename'), ['type' => 'submit',
					'class' => 'italic strong list-group-item']),
			]);

			//CHtml::createLink(
			//	get_param($item, 'sitename'),
			//	$link, ['class' => 'list-group-item italic']
			//);

			//$query = [
			//	'uid' => $uid,
			//	'data' => get_param($this->authdata, 'fullname'),
			//];
			//curl_setopt($req, CURLOPT_URL, $link . 'auth/');
			//curl_setopt($req, CURLOPT_HEADER, 1);
			//curl_setopt($req, CURLOPT_RETURNTRANSFER, 1);
			//curl_setopt($req, CURLOPT_FOLLOWLOCATION, 1);
			//curl_setopt($req, CURLOPT_POST, 1);
			//curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($query));
			//$out = curl_exec($req);
			//var_dump($out);
		}


		if (in_array($uid, [80001681, 80001511, 80001571]))
			$this->data['siteList'] .= CHtml::createLink('Административная панель', '/admin/', [
				'class' => 'list-group-item',
			]);

		$this->render('panel');
	}
}