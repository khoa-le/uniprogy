<?php
class WBaseInstallInstall extends UInstallWorklet
{	
	public function taskModuleFilters()
	{
		return array(
			'base' => 'base.main',
		);
	}
}