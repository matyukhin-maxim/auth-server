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

		/**
		 * Шифрование
		 * Шифруем сериализованный массив, состоящий из идентификатора пользователя и текущей даты
		 * тогда ссылки будут иметь разный вид каждый день, чтобы исключить запоминание в браузере (истории)
		 * при расшифровке дата будет проверятся, и при не совпадении отбрасываться
		 */

		$secure = [$uid, date('Y-m-d')];

		foreach ($sites as $item) {

			$link = get_param($item, 'link');
			$name = get_param($item, 'sitename');
			$key = get_param($item, 'passkey');

			$cipherText = Cipher::encode($secure, $key, true);
			$res = Cipher::decode($cipherText, $key, true);
			var_dump($res);

			$this->data['siteList'] .= CHtml::createLink($name, null, [
				'href' => $link . "auth/openid/" . urlencode($cipherText),
				'class' => 'list-group-item strong italic',
			]);

			/*
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
			*/
		}

		$this->render('panel', false);
		$this->render('');
	}
}