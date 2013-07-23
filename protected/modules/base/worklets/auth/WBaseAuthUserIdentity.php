<?php
class WBaseAuthUserIdentity extends USystemWorklet
{
	public $owner;
	
	public function __call($name,$parameters)
	{
		try {
			return parent::__call($name,$parameters);
		} catch(CException $e) {
			return null;
		}
	}
	
	public function init()
	{
		$args = func_get_args();
		$this->__call('init',$args);
	}
	
	public function taskAuthenticate()
	{
		$owner = $this->owner;		
		if($owner->username !== app()->params['adminLogin'])
			$owner->errorCode=UUserIdentity::ERROR_USERNAME_INVALID;
		else if($owner->password !== app()->params['adminPassword'])
			$owner->errorCode=UUserIdentity::ERROR_PASSWORD_INVALID;
		else
			$owner->errorCode=UUserIdentity::ERROR_NONE;
		return !$owner->errorCode;
	}
	
	public function taskGetRole()
	{
		return 'administrator';
	}
}