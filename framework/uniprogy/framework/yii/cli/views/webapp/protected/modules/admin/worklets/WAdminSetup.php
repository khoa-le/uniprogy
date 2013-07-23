<?php
class WAdminSetup extends UWidgetWorklet
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
		wm()->add('base.admin.appParams');
		$config = require($GLOBALS['config']);
		$modules = $this->getModulesEnabled($config['modules']);
		app()->setModules($modules);
		reset($modules);
		foreach($modules as $name=>$module)
			if(isset($module['params']) && count($module['params']))
				wm()->add($name . '.admin.params');
		parent::taskConfig();
	}
	
	public function taskBreadCrumbs()
	{
		return array(
			$this->t('Setup') => url('/admin/setup')
		);
	}
	
	public function getModulesEnabled($modules)
	{
		foreach($modules as $k=>$v)
		{
			$modules[$k]['enabled'] = true;
			if(isset($modules[$k]['modules']) 
				&& is_array($modules[$k]['modules'])
				&& count($modules[$k]['modules']))
					$modules[$k]['modules'] = $this->getModulesEnabled($modules[$k]['modules']);
		}
		return $modules;
	}
}