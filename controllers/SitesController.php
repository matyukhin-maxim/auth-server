<?php

/**
 * Created by PhpStorm.
 * User: fellix
 * Date: 28.01.16
 * Time: 20:51
 */
class SitesController extends CController {

	public function actionIndex () {

		if (!$this->authdata) $this->redirect('/login/');
		$this->render('');
	}
	
}