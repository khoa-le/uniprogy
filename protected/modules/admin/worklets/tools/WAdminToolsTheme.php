<?php
class WAdminToolsTheme extends UFormWorklet
{	
	public $modelClassName = 'MAdminToolsThemeModel';
	
	public function accessRules()
	{
		return array(
			array('allow', 'roles' => array('administrator')),
			array('deny', 'users'=>array('*'))
		);
	}
	
	public function title()
	{
		return $this->t('Theme Creator');
	}
	
	public function description()
	{
		return $this->t('This tool creates a ZIP package that contains all templates that are being used in a script.');
	}
	
	public function properties()
	{
		return array(
			'activeForm' => array(
				'class'=>'UActiveForm',
				'ajax'=>false
			),
			'description' => $this->description(),
			'elements' => array(
				'name' => array('type' => 'text'),
			),
			'model' => $this->model,
			'buttons' => array(
				'submit' => array('type' => 'submit', 'label' => $this->t('Create Theme Package')),
			),
		);
	}
	
	public function taskSave()
	{
		$name = ucfirst(strtolower($this->model->name));
		$config = require(app()->basePath.DS.'config'.DS.'public'.DS.'modules.php');
		$files = $this->searchModule(null,$config);
		
		$theme = $this->renderFile($this->module->basePath .DS. 'data' .DS. 'theme.php',array(
			'name' => $this->model->name
		), true);
			
		$file = app()->basePath .DS. 'runtime' .DS. $this->model->name.'Theme.php';
		$f = fopen($file, 'w');
		fwrite($f,$theme);
		fclose($f);
		
		$files[$file] = 'protected'.DS.'components'.DS.$this->model->name.'Theme.php';
		
		$zipFile = app()->basePath.DS.'runtime'.DS.'theme.zip';
		if(file_exists($zipFile))
			@unlink($zipFile);
		$zip = Yii::createComponent(array('class'=>'uniprogy.extensions.zip.EZip'));
		$zip->makeZipFromFiles($files,$zipFile);
		
		app()->file->set($zipFile)->send();
		app()->end();
	}
	
	public function searchModule($module,$config,$parents=array())
	{
		if($module)
			$parents[] = $module;
		
		$path = app()->basePath;
		if(count($parents))
			$path.= DS.'modules'.DS.implode(DS.'modules'.DS,$parents);
		$path.= DS.'views';
		
		$replace = 'themes'.DS.strtolower($this->model->name).DS.'views';
		$replaceWorklets = $replace.DS.'worklets';
		if(count($parents))
		{
			$replace.= DS.implode(DS,$parents);
			$replaceWorklets.= DS.implode(DS,$parents);
		}
			
		$files = $this->files($path,$replace);
		foreach($files as $k=>$v)
			if(strpos($v,DS.'worklets'.DS)!==false)
				unset($files[$k]);
				
		$files = array_merge($files,$this->files($path.DS.'worklets',$replaceWorklets));
		
		if(isset($config['modules']) && is_array($config['modules']) && count($config['modules']))
			foreach($config['modules'] as $m=>$c)
				$files = array_merge($files,$this->searchModule($m,$c,$parents));
				
		return $files;
	}
	
	public function files($path,$replace)
	{
		$files = array();
		
		$search = app()->file->set($path)->getContents(true,'/^(?:(?!\.svn).)*$/');
		if(!is_array($search))
			$search = array();
			
		foreach($search as $k=>$v)
			if(is_file($v))
				$files[$v] = str_replace($path,$replace,$v);
			
		return $files;
	}
	
	public function taskRenderOutput()
	{
		parent::taskRenderOutput();
		$this->render('instruction');
	}
	
	public function taskBreadCrumbs()
	{
		return array(
			$this->t('Tools') => url('/admin/tools'),
			$this->title()
		);
	}
}