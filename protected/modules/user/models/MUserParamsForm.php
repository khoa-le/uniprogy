<?php
class MUserParamsForm extends UFormModel
{
	public $emailVerification;
	public $approveNewAccounts;
	public $unverifiedAccess;
	public $unapprovedAccess;
	public $verificationTimeLimit;
	public $passwordResetTimeLimit;
	public $captcha;
	public $inactivityTimeLimit;
	public $fileTypes;
	public $fileSizeLimit;
	public $fileResize;	
	
	public function module()
	{
		return 'user';
	}
	
	public function rules()
	{
		return array(
			array(implode(',',array_keys(get_object_vars($this))),'safe')
		);
	}
}