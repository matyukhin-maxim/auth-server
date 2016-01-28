<?php

class IndexController extends CController {

	public function actionIndex() {

		$this->redirect($this->authdata ? '/sites/' : '/login/');
	}

}
