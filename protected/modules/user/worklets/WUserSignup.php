<?php
class WUserSignup extends UFormWorklet
{
	public $modelClassName = 'MUserSignupForm';
	
	public function accessRules()
	{
		return array(array('allow','users'=>array('*')));
	}
	
	public function title()
	{
		return $this->t('Sign Up');
	}
	
	public function properties()
	{
		return array(
			'action' => array('/user/signup'),
			'elements' => array(
				'firstName' => array('type' => 'text'),
				'lastName' => array('type' => 'text'),
				'email' => array('type' => 'text'),
				'password' => array('type' => 'password'),
				'passwordRepeat' => array('type' => 'password'),
				'<hr />',
				'termsAgree' => array(
					'type' => 'checkbox',
					'layout' => "<fieldset>{input}\n{label}\n{hint}</fieldset>",
					'uncheckValue' => '',
					'required' => false,
					'afterLabel' => '',
				),
			),
			'buttons' => array(
				'submit' => array('type' => 'submit',
					'label' => $this->t('Sign Up'))
			),
			'model' => $this->model
		);
	}
	
	public function beforeBuild()
	{
		if($this->param('captcha'))
			$this->attachBehavior('base.captcha','base.captcha');
	}
	
	public function taskConfig()
	{
		parent::taskConfig();			
		wm()->add('base.dialog');
	}
	
	public function taskSave()
	{
		$valid = parent::taskSave();
		
		// find user model
		$userModel = null;
		$models = $this->form->getModels();
		foreach($models as $model)
		{
			if($model instanceOf MUser) {
				$userModel = $model;
				break;
			}
		}		
		
		if($this->getModule()->params['emailVerification']){
			// generate verification hash
			$hash = UHelper::hash();
			// save hash in a DB
			$model = new MHash;
			$model->hash = $hash;
			$model->type = 1;
			$model->id   = $userModel->id;
			$model->expire = $this->getModule()->params['verificationTimeLimit']
				? time() + $this->getModule()->params['verificationTimeLimit'] * 3600
				: 0;
			$model->save();
			
			// send verification email
			app()->mailer->send($userModel, 'verificationEmail', array('link' => aUrl(
				'/user/verify', array(
					'h' => $hash,
					'e' => $userModel->email,
				)))
			);
		}
		
		$flash = $this->t('Your account has been successfully created!');
		switch($userModel->role)
		{
			case 'unverified':
				$flash.= ' ' . $this->t('Please follow instructions from our email to verify your account.');
				break;
			case 'unapproved':
				$flash.= ' ' . $this->t('You will be able to login and start using your account once it will be approved by admin.');
				break;
		}
		
		app()->user->setFlash('info', $flash);
		
		return $valid && true;
	}
}