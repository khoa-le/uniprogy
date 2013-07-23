<?php
class MUserRestoreForm extends UFormModel
{
	public $email;
	
	public function module()
	{
		return 'user';
	}
	
	public function rules()
	{
		return array(
			array('email', 'required'),
			array('email', 'validUserId')
		);
	}
	
	public function attributeLabels()
	{
		return array(
			'email' => $this->t('Email')
		);
	}
	
	public function validUserId($attribute,$params)
	{
		$ui = new UUserIdentity(0,0);
		$valid = MUser::model()->exists('LOWER(email)=:u', array(
			':u' => $this->email
		));
		if(!$valid)
			$this->addError('email', $this->t('User not found. Please verify your input.'));
	}
}