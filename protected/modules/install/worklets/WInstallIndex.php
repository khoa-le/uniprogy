<?php
class WInstallIndex extends UFormWorklet
{
	public $modelClassName = 'MInstallLoginForm';
	
	public function title()
	{
		return $this->t('Installer Login');
	}
	
	public function accessRules()
	{
		return array(array('allow','users'=>array('*')));
	}
	
	public function properties()
	{
		return array(
			'elements' => array(
				'username' => array('type' => 'text'),
				'password' => array('type' => 'password'),
			),
			'buttons' => array(
				'submit' => array('type' => 'submit', 'label' => $this->t('Login'))
			),
			'model' => $this->model
		);
	}
	
	public function beforeBuild()
	{
		if(app()->user->checkAccess('administrator'))
			app()->request->redirect($this->successUrl());
	}
	
	public function taskSave()
	{
		$model = $this->form->model;
		if(!$model->hasErrors())  // we only want to authenticate when no input errors
		{
			$identity=new UUserIdentity($model->username,$model->password);
			$errorString = $identity->authenticate();

			if(is_string($errorString))
				return $model->addError('username', $errorString);
			
			switch($identity->errorCode)
			{
				case UUserIdentity::ERROR_NONE:
					app()->user->login($identity,0);
					break;
				case UUserIdentity::ERROR_USERNAME_INVALID:
					$model->addError('username', $this->t('Username or password is incorrect.'));
					break;
				case UUserIdentity::ERROR_PASSWORD_INVALID:
					$model->addError('username', $this->t('Username or password is incorrect.'));
					break;
			}
			
			if(!$model->hasErrors('username') && !app()->user->checkAccess('administrator',array(),false))
				$model->addError('username', $this->t('Username or password is incorrect.'));
		}
	}
	
	public function successUrl()
	{
		return url('/install/home');
	}
}