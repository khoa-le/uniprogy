<?php
class MUserAccountForm extends MUser
{
	public $newPassword;
	public $passwordRepeat;
	
	public function rules()
	{
		return array(
			array('firstName,lastName,email,password','required'),
			array('firstName,lastName,email', 'length', 'max' => 250),
			array('avatar,timeZone', 'safe'),
			array('email', 'email'),
			array('email', 'unique', 'className' => 'MUser', 'message' => $this->t('This email is already in use.')),
			array('newPassword', 'length', 'min' => 6),
			array('passwordRepeat', 'compare', 'compareAttribute' => 'newPassword'),
			array('role','safe','on'=>'admin'),
		);
	}
	
	public function attributeLabels()
	{
		return array(
			'avatar' => $this->t('Avatar'),
			'firstName' => $this->t('First Name'),
			'lastName' => $this->t('Last Name'),
			'email' => $this->t('Email'),
			'password' => $this->t('Password'),
			'newPassword' => $this->t('New Password'),
			'passwordRepeat' => $this->t('New Password Confirm'),
			'timeZone' => $this->t('Time Zone'),
		);
	}
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function beforeValidate()
	{
		if(!$this->avatar)
			$this->avatar = null;
			
		if($this->newPassword)
		{
			$this->password = $this->newPassword;
			$this->scenario = 'password';
		}
		return parent::beforeValidate();
	}
}