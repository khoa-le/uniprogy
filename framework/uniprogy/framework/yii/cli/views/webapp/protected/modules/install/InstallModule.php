<?php
class InstallModule extends UWebModule
{
	public function preinit()
	{
		$this->setImport(array($this->getName().'.components.*',$this->getName().'.models.*'));
		return parent::preinit();
	}
	
	public function getTitle()
	{
		return 'Install';
	}
	
	public function getVersion()
	{
		return $this->param('version');
	}
}
