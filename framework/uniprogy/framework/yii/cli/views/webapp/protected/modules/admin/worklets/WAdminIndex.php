<?php
class WAdminIndex extends UFormWorklet
{
	public $modelClassName = 'MAdminLoginForm';
	
	public function title()
	{
		return $this->t('Admin Panel Login');
	}
	
	public function accessRules()
	{
		return array(array('allow','users'=>array('*')));
	}
	
	public function properties()
	{
		return array(
			'elements' => array(
				'email' => array('type' => 'text'),
				'password' => array('type' => 'password'),
				'rememberMe' => array('type' => 'checkbox'),
			),
			'buttons' => array(
				'submit' => array('type' => 'submit', 'label' => $this->t('Login'))
			),
			'model' => $this->model
		);
	}
	
	public function taskSave()
	{
		$model = $this->form->model;
		if(!$model->hasErrors())  // we only want to authenticate when no input errors
		{
			$identity=new UUserIdentity($model->email,$model->password);
			$errorString = $identity->authenticate();
			
			if(is_string($errorString))
				return $model->addError('email', $errorString);
			
			switch($identity->errorCode)
			{
				case UUserIdentity::ERROR_NONE:
					$duration=$model->rememberMe ? 3600*24*30 : 0; // 30 days
					app()->user->login($identity,$duration);
					break;
				case UUserIdentity::ERROR_USERNAME_INVALID:
					$model->addError('email', $this->t('Email or password is incorrect.'));
					break;
				case UUserIdentity::ERROR_PASSWORD_INVALID:
					$model->addError('email', $this->t('Email or password is incorrect.'));
					break;
			}
			
			if(!$model->hasErrors('email') && !app()->user->checkAccess('administrator',array(),false))
				$model->addError('email', $this->t('Email or password is incorrect.'));
		}
	}
	
	public function successUrl()
	{
		return url('/admin');
	}
}