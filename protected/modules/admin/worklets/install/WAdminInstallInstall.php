<?php
class WAdminInstallInstall extends UInstallWorklet
{	
	public function taskModuleFilters()
	{
		return array(
			'base' => 'admin.main',
			'admin' => 'admin.main',
			'user' => 'admin.main',
		);
	}
}