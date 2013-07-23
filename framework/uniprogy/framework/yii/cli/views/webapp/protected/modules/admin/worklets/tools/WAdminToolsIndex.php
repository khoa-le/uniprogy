<?php
class WAdminToolsIndex extends UWidgetWorklet
{
	public $tools;
	
	public function accessRules()
	{
		return array(
			array('allow', 'roles' => array('administrator')),
			array('deny', 'users'=>array('*'))
		);
	}
	
	public function title()
	{
		return $this->t('Tools');
	}
	
	public function taskConfig()
	{
		$this->tools = array(
			'admin.tools.message',
			'admin.tools.theme',
			'admin.tools.optimize',
			'admin.tools.cron'
		);
		wm()->add('admin.tools.menu',null,array('tools'=>$this->tools));
		parent::taskConfig();
	}
	
	public function taskRenderOutput()
	{		
		$this->render('list');
	}
}