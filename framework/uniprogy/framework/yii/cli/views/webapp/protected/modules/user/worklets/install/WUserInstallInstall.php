<?php
class WUserInstallInstall extends UInstallWorklet
{
	public $modelClassName = 'MUserInstallForm';
	public $autoSubmit = false;
	
	public function init()
	{
		Yii::import('application.modules.user.models.MUserInstallForm');
		return parent::init();
	}
	
	public function properties()
	{
		return array(
			'elements' => array(
				'name' => array('type' => 'text'),
				'email' => array('type' => 'text'),
				'password' => array('type' => 'password'),
				'passwordRepeat' => array('type' => 'password'),
			),
			'buttons' => array(
				'submit' => array('type' => 'submit',
					'label' => $this->t('Create Admin Account'))
			),
			'model' => $this->model
		);
	}
	
	public function taskSave()
	{
		parent::taskSave();
		
		if($this->model->hasErrors())
			return false;
			
		$user = new MUser;
		$user->scenario = 'password';
		$user->name = $this->model->name;
		$user->email = $this->model->email;
		$user->password = $this->model->password;
		$user->role = 'administrator';
		$user->save();
			
		app()->user->worklet = 'user.auth.webUser';
		app()->user->init();
		
		$identity=new UUserIdentity($this->model->email,$this->model->password);
		$identity->worklet = 'user.auth.userIdentity';
		$identity->init();
		
		$errorString = $identity->authenticate();
		
		if(is_string($errorString))
			return $this->model->addError('email', $errorString);
		
		switch($identity->errorCode)
		{
			case UUserIdentity::ERROR_NONE:
				app()->user->login($identity,0);
				break;
			case UUserIdentity::ERROR_USERNAME_INVALID:
				$this->model->addError('email', $this->t('Email or password is incorrect.'));
				break;
			case UUserIdentity::ERROR_PASSWORD_INVALID:
				$this->model->addError('email', $this->t('Email or password is incorrect.'));
				break;
		}
	}
	
	public function report()
	{
		return $this->render('report',array(
			'email'=>$this->model->email,'password'=>$this->model->password),true);
	}
	
	public function taskModuleParams()
	{
		return CMap::mergeArray(parent::taskModuleParams(),array(
			'emailVerification' => '1',
	        'approveNewAccounts' => '0',
	        'unverifiedAccess' => '1',
	        'unapprovedAccess' => '1',
	        'verificationTimeLimit' => '48',
	        'passwordResetTimeLimit' => '24',
	        'captcha' => '1',
	        'inactivityTimeLimit' => '15',
	        'fileTypes' => 'jpg, gif, png',
	        'fileSizeLimit' => '4',
	        'fileResize' => '100x100',
		));
	}
	
	public function taskModuleFilters()
	{
		return array(
			'base' => 'user.main',
			'admin' => 'user.main',
			'user' => 'user.main',
		);
	}
	
	public function taskModuleAuth()
	{
		return array(
			'items' => array(
				'user' => array(2, 'User', NULL, NULL),
				'unverified' => array(2, 'Unverified', NULL, NULL),
				'banned' => array(2, 'Banned', NULL, NULL),
				'unapproved' => array(2, 'Unapproved', NULL, NULL),
			),
			'children' => array(
				'administrator' => array('user'),
				'user' => array('guest'),
				'unverified' => array('guest'),
				'banned' => array('guest'),
				'unapproved' => array('guest'),
			),
		);
	}
}