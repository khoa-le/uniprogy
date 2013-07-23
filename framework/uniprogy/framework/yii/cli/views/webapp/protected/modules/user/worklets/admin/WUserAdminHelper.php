<?php
class WUserAdminHelper extends USystemWorklet
{
	public function taskRoles()
	{
		$roles = array('administrator'=>1,'user'=>1,'unapproved'=>1,'unverified'=>1,'banned'=>1);
		foreach($roles as $k=>$v)
		{
			$authItem = app()->authManager->getAuthItem($k);
			$roles[$k] = $authItem->description;
		}
		reset($roles);
		return $roles;
	}
	
	public function taskGetRole($name)
	{
		$authItem = app()->authManager->getAuthItem($name);
		return $authItem->description;
	}
}