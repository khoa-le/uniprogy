<?php
class UApp extends UWebApplication
{
	public function getVersion()
	{
		return '1.0.0';
	}
	
	public function getTitle()
	{
		return 'UniProgy Application';
	}
	
	public function getAppModules()
	{
		return array(
			'admin',
			'base',
			'location',
			'user'
		);
	}
	
	public function getVersionHistory()
	{
		return array(
			'1.0.0',
		);
	}
}