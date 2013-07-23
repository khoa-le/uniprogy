<?php
class WInstallProcess extends UWidgetWorklet
{
	public $show = false;
	
	public function accessRules()
	{
		return array(
			array('allow', 'roles' => array('administrator')),
			array('deny', 'users'=>array('*'))
		);
	}
	
	public function taskConfig()
	{
		wm()->get('install.helper')->loadModules();
		$stack = wm()->get('install.helper')->getInstallers();
		
		if(count($stack))
		{
			$installer = $stack->itemAt(0);
			wm()->add($installer);
			return;
		}
		$this->show = true;
		return parent::taskConfig();
	}
	
	public function taskRenderOutput()
	{
		$this->render('complete');
	}
}