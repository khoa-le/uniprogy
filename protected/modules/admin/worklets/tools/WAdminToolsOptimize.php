<?php
class WAdminToolsOptimize extends UFormWorklet
{	
	public $modelClassName = 'UDummyModel';
	
	public $files;
	public $data;
	public $classes=array();
	public $types = array('css','js');
	
	public function accessRules()
	{
		return array(
			array('allow', 'roles' => array('administrator')),
			array('deny', 'users'=>array('*'))
		);
	}
	
	public function title()
	{
		return $this->t('Optimizer');
	}
	
	public function description()
	{
		return $this->t('This tool optimizes performance of your site.');
	}
	
	public function properties()
	{
		return array(
			'activeForm' => array(
				'class'=>'UActiveForm',
				'ajax'=>false
			),
			'description' => $this->description(),
			'elements' => array(),
			'model' => $this->model,
			'buttons' => array(
				'submit' => array('type' => 'submit', 'label' => $this->t('Run Optimizer')),
			),
		);
	}
	
	public function taskSave()
	{
		$this->collectData(Yii::getPathOfAlias('uniprogy.framework'),'js');
		$this->collect(app());
		$this->send();
		app()->end();
	}
	
	public function collect($parent)
	{
		$modules = $parent->getModules();
		foreach($modules as $m=>$c)
		{
			$module = $parent->getModule($m);
			if($module)
			{
				$path = $parent->basePath .DS. 'modules' .DS. $m;
				reset($this->types);
				foreach($this->types as $type)
					$this->collectData($path,$type);
				$this->collectClasses($path);
				$this->collect($parent->getModule($m));
			}
		}
	}
	
	public function collectClasses($folder)
	{
		$types = array('components','models');
		while(list($dummy,$type) = each($types))
		{
			$files = app()->file->set($folder.DS.$type)->contents;
			if(!is_array($files))
				$files = array();
			foreach($files as $f)
			{
				$name = str_replace('.php','',basename($f));
				if(strpos($name,'.')===false)
					$this->classes[$name] = str_replace(app()->basePath,'',$f);
			}
		}
	}
	
	public function collectData($folder,$type)
	{
		if(file_exists($folder .DS. $type))
		{		
			$folder = $folder .DS. $type;
			$dir = opendir($folder);
			while($file = readdir($dir))
			{
				if(!in_array($file,array('.','..')) && is_file($folder.'/'.$file))
				{
					$this->pushData(file_get_contents($folder.'/'.$file),$type);
					$this->pushFile($file,$type);
				}
			}
			closedir($dir);
		}
	}
	
	public function pushData($data,$type)
	{
		if(!isset($this->data[$type]))
			$this->data[$type] = '';
		$this->data[$type].= $data."\n";
	}
	
	public function pushFile($file,$type)
	{
		if(!isset($this->files[$type]))
			$this->files[$type] = array();
		$this->files[$type][] = $file;
	}
	
	public function send()
	{
		$files = array();
		
		reset($this->types);
		foreach($this->types as $type)
		{
			$file = app()->basePath .DS. 'runtime' .DS. 'all.' . $type;
			$f = fopen($file, 'w');
			fwrite($f, $this->data[$type]);
			fclose($f);		
			$files[$file] = 'protected/assets/all.'.$type;
		}
		
		reset($this->types);
		$BBaseOptimize = $this->renderFile($this->module->basePath .DS. 'data' .DS. 'optimize.php',
			array('types' => $this->types, 'files' => $this->files, 'classes' => $this->classes), true);
		
		$file = app()->basePath .DS. 'runtime' .DS. 'BBaseOptimize.php';
		$f = fopen($file, 'w');
		fwrite($f,$BBaseOptimize);
		fclose($f);
		
		$files[$file] = 'protected/modules/base/behaviors/BBaseOptimize.php';
		
		$zipFile = app()->basePath.DS.'runtime'.DS.'optimize.zip';
		if(file_exists($zipFile))
			@unlink($zipFile);
		$zip = Yii::createComponent(array('class'=>'uniprogy.extensions.zip.EZip'));
		$zip->makeZipFromFiles($files,$zipFile);
		
		app()->file->set($zipFile)->send();
		app()->end();
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