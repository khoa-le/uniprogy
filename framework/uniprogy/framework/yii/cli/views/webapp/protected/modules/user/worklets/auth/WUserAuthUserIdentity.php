<?php
Yii::import('application.modules.base.worklets.auth.WBaseAuthUserIdentity');
class WUserAuthUserIdentity extends WBaseAuthUserIdentity
{	
	protected $_id;
	public $model;
	
	public function taskAuthenticate()
	{
		$owner = $this->owner;
		
		$this->model = $user = MUser::model()->find('LOWER(email)=:u', array(
			':u' => $owner->username
		));
		if($user === null || UHelper::password($owner->password, $user->salt) !== $user->password)
			return !($owner->errorCode = UUserIdentity::ERROR_USERNAME_INVALID);
		
		switch($user->role)
		{
			case 'unverified':
				if($this->param('unverifiedAccess'))
					return $this->t('Please verify your email first. {clickHere} to get your verification email resent.',array(
						'{clickHere}' => CHtml::link($this->t('Click here'), array('/user/resend'))
					));
				break;
			case 'banned':
				return $this->t('Your account is banned!');
				break;
			case 'unapproved':
				if($this->param('unapprovedAccess'))
					return $this->t('Your account is awaiting for admin approval.');
				break;
		}
			
		$this->_id = $user->id;
		return !($owner->errorCode = UUserIdentity::ERROR_NONE);
	}
	
	public function taskGetId()
	{
		return $this->_id;
	}
	
	public function taskGetRole()
	{
		if($this->model!==null)
			return $this->model->role;
	}
}