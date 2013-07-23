<?php
class MUserInstallForm extends UFormModel
{
	public $name;
	public $email;
	public $password;
	public $passwordRepeat;
	
	public function module()
	{
		return 'user';
	}
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function rules()
	{
		return array(
			array('email, password, passwordRepeat, name', 'required'),
			array('name, email', 'length', 'max' => 250),
			array('email', 'email'),
			array('password', 'length', 'min' => 6),
			array('passwordRepeat', 'compare', 'compareAttribute'=>'password'),
		);
	}
	
	public function attributeLabels()
	{
		return array(
			'name' => $this->t('Full Name'),
			'email' => $this->t('Email'),
			'password' => $this->t('Password'),
			'passwordRepeat' => $this->t('Password again'),
		);
	}
	
	public function beforeSave()
	{
		if($this->isNewRecord){
			// set role
			$this->role = 'administrator';
			// encrypt password
			$this->salt = UHelper::salt();
			$this->password = UHelper::password($this->password, $this->salt);
		}
		return parent::beforeSave();
	}
}