<?php
class WUserLogin extends UFormWorklet
{
	public $modelClassName = 'MUserLoginForm';
	
	public function accessRules()
	{
		return array(array('allow','users'=>array('*')));
	}
	
	public function title()
	{
		return $this->t('Sign In');
	}
	
	public function properties()
	{
		return array(
			'description' => $this->t('Don\'t have account? {register}', array(
				'{register}' => CHtml::link($this->t('Create one!'), array('/user/signup')),
			)),
			'action' => array('/user/login'),
			'elements' => array(
				'email' => array('type' => 'text'),
				'password' => array(
					'type' => 'password', 
					'hint' => CHtml::link(
						t('Forgot your password?', 'user'),
						url('/user/restore')
				)),
				'rememberMe' => array(
					'type' => 'checkbox',
					'layout' => "<fieldset>{input}\n{label}\n{hint}</fieldset>",
					'uncheckValue' => '',
					'required' => false,
					'afterLabel' => '',
				),
			),
			'buttons' => array(
				'submit' => array('type' => 'submit',
					'label' => $this->t('Login'))
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
		}		
	}
	
	public function successUrl()
	{
		return app()->user->returnUrl;
	}
}