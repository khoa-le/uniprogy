<?php
class MBaseCaptchaForm extends UFormModel
{
	public $verifyCode;
	
	public function module()
	{
		return 'base';
	}
	
	public function rules()
	{
		return array(
			array('verifyCode', 'required', 'message' => $this->t('Please input verification code.')),
			array('verifyCode', 'captcha', 'captchaAction' => '/system/captcha')
		);
	}
	
	public function attributeLabels()
	{
		return array(
			'verifyCode' => $this->t('Please enter the letters as they are shown in the image above.')
		);
	}
}