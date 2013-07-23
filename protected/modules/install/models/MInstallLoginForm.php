<?php
class MInstallLoginForm extends UFormModel
{
	public $username;
	public $password;
	
	public function module()
	{
		return 'install';
	}
	
	public function rules()
	{
		return array(
			array('username,password', 'required')
		);
	}
	
	public function attributeLabels()
	{
		return array(
			'username' => $this->t('Username'),
			'password' => $this->t('Password'),
		);
	}
}