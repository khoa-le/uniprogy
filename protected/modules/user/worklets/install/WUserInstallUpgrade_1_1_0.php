<?php
class WUserInstallUpgrade_1_1_0 extends UInstallWorklet
{
	public $fromVersion = '1.0.2';
	public $toVersion = '1.1.0';
	
	public function taskSuccess()
	{
		app()->db->createCommand("UPDATE `{{User}}` ")->execute();
		return parent::taskSuccess();
	}
}