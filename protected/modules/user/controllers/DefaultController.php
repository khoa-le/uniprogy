<?php
class DefaultController extends UController
{	
	public function actionLogout()
	{
		app()->user->logout(false);
		app()->request->redirect(aUrl('/'));
	}
}