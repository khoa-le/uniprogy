<?php
class FAdminMain extends UWorkletFilter
{	
	public function filters()
	{
		return array(
			'admin.index' => array(
				'replace' => 'AdminReplace',
				'cacheKey' => 'AdminCacheKey'
			),
			'user.menu' => array(
				'behaviors' => 'AdminMenu',
				'cacheKey' => 'AdminCacheKey'
			),
			'base.init' => array(
				'behaviors' => 'AdminTheme',
				'cacheKey' => 'AdminCacheKey'
			),
		);
	}
	
	public function AdminTheme()
	{
		$b = array('admin.rules');
		if(app()->user->checkAccess('administrator'))
			$b[] = 'admin.theme';
		return $b;
	}
	
	public function AdminMenu()
	{
		if(app()->user->checkAccess('administrator'))
			return array('admin.menu');
	}
	
	public function AdminReplace()
	{
		if(app()->user->checkAccess('administrator'))
			return 'admin.home';
	}
	
	public function AdminCacheKey()
	{
		return array('admin.' . (int)(app()->user->checkAccess('administrator')));
	}
}