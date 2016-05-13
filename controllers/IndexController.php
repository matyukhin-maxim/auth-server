<?php

class IndexController extends CController {

	public function actionIndex() {

		$uid = get_param($this->authdata, 'id');

		if ($uid) $this->redirect('/sites/');
		else $this->redirect('/login/');

		//$this->redirect($this->authdata ? '/sites/' : '/login/');
		//$this->redirect('/admin/');
	}

}
