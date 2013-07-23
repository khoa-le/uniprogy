<?php
class MUserResetForm extends UFormModel
{
	public $hash;
	public $user;
	public $newPassword;
	public $passwordRepeat;
	
	public function module()
	{
		return 'user';
	}
	
	public function rules()
	{
		return array(
			array('newPassword, passwordRepeat', 'required'),
			array('newPassword', 'length', 'min' => 6),
			array('passwordRepeat', 'compare', 'compareAttribute' => 'newPassword'),
			array('hash', 'validHash'),
			array('user', 'safe')
		);
	}
	
	public function attributeLabels()
	{
		return array(
			'newPassword' => $this->t('New Password'),
			'passwordRepeat' => $this->t('Re-type Password'),
		);
	}
	
	public function validHash($attribute,$params)
	{
		$valid = MHash::model()->exists('hash=? AND id = ? AND type = ? AND expire >= ?', array(
			$this->hash, $this->user, 2, time()
		));
		if(!$valid)
			$this->addError('newPassword', $this->t('This password reset link is inactive.'));
	}
}