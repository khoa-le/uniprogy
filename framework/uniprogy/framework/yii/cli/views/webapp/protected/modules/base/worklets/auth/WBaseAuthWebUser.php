<?php
class WBaseAuthWebUser extends USystemWorklet
{
	public $owner;
	
	public function __call($name,$parameters)
	{
		try {
			return parent::__call($name,$parameters);
		} catch(CException $e) {
			return null;
		}
		return null;
	}
	
	public function init()
	{
		$args = func_get_args();
		$this->__call('init',$args);
	}
	
	public function taskInit()
	{
		$this->owner->loginUrl = array('/');
	}
	
	public function taskLogin($identity,$duration=0)
	{
		static $call;
		if(!isset($call))
		{
			$call = true;
			$this->owner->login($identity,$duration);
			app()->authManager->assign(app()->user->role, app()->user->id);
		}
		return null;
	}
	
	public function taskGetRole()
	{
		return 'administrator';
	}
}