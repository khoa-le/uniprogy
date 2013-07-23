<?php
class AdminModule extends UWebModule
{
	public function getTitle()
	{
		return 'Admin';
	}
	
	public function getRequirements()
	{
		return array('user' => self::getVersion());
	}
}