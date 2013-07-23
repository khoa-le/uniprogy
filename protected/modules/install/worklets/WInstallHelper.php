<?php
class WInstallHelper extends USystemWorklet
{
	public function taskClearReports()
	{
		$this->session = array('reports' => array());
	}
	
	public function taskAddReport($data)
	{
		$session = $this->session;
		$reports = isset($session['reports'])?$session['reports']:array();
		$reports[] = $data;
		$this->session = array('reports' => $reports);
	}
	
	public function taskGetReports()
	{
		return $this->session['reports'];
	}
	
	public function taskAddInstaller($installer)
	{
		$this->removeInstaller($installer);
		$installers = $this->getInstallers();
		$installers->insertAt(0,$installer);
		$this->session = array('installers' => $installers);
	}
	
	public function taskRemoveInstaller($installer)
	{
		$installers = $this->getInstallers();
		$installers->remove($installer);
		$this->session = array('installers' => $installers);
	}
	
	public function taskClearInstallers()
	{
		$this->session = array('installers' => new CList);
	}
	
	public function taskGetInstallers()
	{
		$session = $this->session;
		return isset($session['installers'])?$session['installers']:new CList;
	}
	
	public function taskLoadModules()
	{
		$config = require(app()->basePath.DS.'config'.DS.'public'.DS.'modules.php');
		$modules = CMap::mergeArray($config['modules'],$this->findModules());
		app()->setModules($modules);
	}
	
	public function taskFindModules($dir=null)
	{
		$modules = array();
		$dir=$dir?$dir:'application.modules';
		$dh = app()->file->set(Yii::getPathOfAlias($dir));
		$dirs=$dh->exists?$dh->getContents(false):array();
		
		foreach($dirs as $mdir)
		{
			$name = substr($mdir,strrpos($mdir,DS)+1);
			$class = ucfirst($name).'Module';
			$file = $mdir.DS.$class.'.php';
			
			if(file_exists($file))
			{
				$module = array('enabled' => true);
				$module['modules'] = $this->findModules($dir.'.'.$name.'.modules');
				$modules[$name] = $module;
			}
		}
		return $modules;
	}
	
	public function taskInstaller($type,$id,$version=null,$autoInstall=false,$requiredBy=null)
	{		
		if($id == 'app')
			foreach(app()->getAppModules() as $m)
				$this->installer($type,$m,$version,true,app());
		
		$installer = $id=='app'?'install.app.':$id.'.install.';
		if($id!='app')
			$module = getModuleFromAlias($id);
		else
			$module = app();
			
		if($type == 'upgrade')
		{			
			$installed = $module->params['version'];
			if(!$installed)
			{
				if(!$autoInstall)
					throw new CHttpException(403,$this->t('You can\'t upgrade this module because it has never been installed before.'));
				else
					$type = 'install';
			}
			else
			{
				$version = $version?$version:$module->getVersion();
				$installer.= $type.'_'.str_replace('.','_',$version);
			}
		}
		
		if($type == 'install')
			$installer.= 'install';
		
		if(!wm()->get($installer))
		{
			$message = $this->t('Unknown Error');
			if($type == 'upgrade')
				$message = $this->t('Upgrader up to version {version} could not be found for {module}.', array(
					'{version}' => $version, '{module}' => $module->getTitle()
				));
			elseif($type == 'install')
				$message = $this->t('Installer could not be found for {module}.', array(
					'{module}' => $module->getTitle(),
				));
				
			if($requiredBy)
				$message.= ' '.$this->t('Required by: {module}.', array('{module}' => $requiredBy->getTitle()));
				
			throw new CHttpException(403,$message);
		}
			
		$this->addInstaller($installer);
	}
}