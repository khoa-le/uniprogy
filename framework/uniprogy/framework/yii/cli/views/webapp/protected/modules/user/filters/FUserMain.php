<?php
class FUserMain extends UWorkletFilter
{
	public function filters()
	{
		return array(
			'base.auth.webUser' => array('replace' => 'authWebUser'),
			'base.auth.userIdentity' => array('replace' => 'authUserIdentity'),
			'admin.menu' => array('behaviors' => array('user.adminMenu')),
			'user.admin.create' => array('replace' => array('user.admin.update')),
		);
	}
	
	public function authWebUser()
	{
		return array('user.auth.webUser');
	}
	
	public function authUserIdentity()
	{
		return array('user.auth.userIdentity');
	}
}