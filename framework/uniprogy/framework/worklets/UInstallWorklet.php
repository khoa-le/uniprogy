<?php
/**
 * UInstallWorklet class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UInstallWorklet is worklet that you can use to create an installer, uninstaller or upgrader
 * for a particular module.
 *
 * @version $Id: UInstallWorklet.php 79 2010-12-10 17:55:00 GMT vasiliy.y $
 * @package system.worklets
 * @since 1.0.0
 */
class UInstallWorklet extends UFormWorklet
{
	/**
	 * Install worklet is a form worklet, but most of the times user isn't supposed to submit anything
	 * so we can simply use a dummy model so processing task doesn't get broken.
	 * If installer actually expects some input from the user you can use this property to 
	 * specify model class name which needs to be associated with the form.
	 * @var string form model class name
	 */
	public $modelClassName = 'UDummyModel';
	/**
	 * @var boolean automatically submit the form if worklet doesn't expect any input from the user
	 */
	public $autoSubmit = true;
	/**
	 * @var string which version does this worklet upgrades module from. Set to false if this is
	 * an installer.
	 * @since 1.1.0
	 */
	public $fromVersion = false;
	/**
	 * @var string which version does this worklet upgrade module to. Set to false if this is
	 * an uninstaller. Leave as NULL if this an installer for an application module.
	 * @see UWebModule::getIsAppModule
	 * @since 1.1.0
	 */
	public $toVersion;
	
	/**
	 * Only administrator can run installer.
	 */
	public function accessRules()
	{
		return array(
			array('allow', 'roles' => array('administrator')),
			array('deny', 'users'=>array('*'))
		);
	}
	
	/**
	 * @return string module/app title. Automatically adds 'Module' if this is a module
	 * installer.
	 */
	public function getModuleTitle()
	{
		$txt = array($this->module->title);
		if($this->module instanceOf UWebModule)
		{
			$txt[] = ' ';
			$txt[] = $this->t('Module');
		}
		return txt()->format($txt);
	}
	
	/**
	 * @return the title of installer.
	 */
	public function title()
	{
		if($this->fromVersion === false)
			$t = '{module} Installation';
		elseif($this->toVersion === false)
			$t = '{module} Un-Installation';
		else
			$t = '{module} Upgrade to {version}';
		return $this->t($t,array(
			'{module}' => $this->moduleTitle, '{version}' => $this->toVersion
		));
	}
	
	/**
	 * Defines preset form properties.
	 * @return form properties.
	 */
	public function properties()
	{
		return array(
			'elements' => array('attribute' => array('type' => 'hidden')),
			'buttons' => array(
				'submit' => array('type' => 'submit',
					'label' => $this->t('Install'))
			),
			'model' => $this->model
		);
	}
	
	/**
	 * Overrides {@link UFormWorklet::taskBuild}.
	 * Installer and Upgrader:
	 * Checks if this module requires other modules installation or upgrade.
	 * Upgrader:
	 * Checks if current module version is lower then {@link fromVersion}
	 * and if it is an upgrader for that lower version will be added to installers stack.
	 * @see identifyPreviousVersion
	 * @see UWebModule::getRequirements
	 */
	public function taskBuild()
	{
		$okToRun = true;
		// upgrader
		if($this->fromVersion && $this->toVersion
			&& version_compare($this->module->param('version'),$this->fromVersion) < 0)
		{
			$pVersion = $this->identifyPreviousVersion();
			if($pVersion)
			{
				$okToRun = false;
				wm()->get('install.helper')->installer('upgrade',UFactory::getModuleAlias($this->module),$pVersion,true,$this->module);
			}
		}
		// installer or upgrader
		if($this->toVersion !== false)
		{
			if($this->module instanceOf UWebModule && ($reqs = $this->module->getRequirementsMet(true))!==true)
			{
				$okToRun = false;
				foreach($reqs as $m=>$v)
					if($v === true)
						wm()->get('install.helper')->installer('install',$m,null,true,$this->module);
					else
						wm()->get('install.helper')->installer('upgrade',$m,$v,true,$this->module);
			}
		}
		// can we proceed with installation/upgrade?
		if(!$okToRun)
		{
			// re-initialize installation process worklet
			wm()->addCurrent('install.process');
			// do not render this worklet
			$this->show = false;
			// stop this worklet building
			$this->show = false;
			return false;
		}
		return parent::taskBuild();
	}
	
	/**
	 * Overrides {@link UFormWorklet::taskConfig} by auto-assigning {@link toVersion} for
	 * application modules.
	 * @return mixed null
	 */
	public function taskConfig()
	{
		if($this->toVersion===null)
			if(!$this->module instanceOf UWebModule || app()->getIsAppModule($this->module))		
				$this->toVersion = app()->getVersion();
			else
				$this->toVersion = $this->module->getVersion();
		return parent::taskConfig();
	}
	
	/**
	 * Creates a form.
	 * @see autoSubmit
	 */
	public function taskCreateForm()
	{
		parent::taskCreateForm();
		if($this->autoSubmit)
			$_POST['yform_'.$this->form->id] = 1;
	}
	
	/**
	 * Processes a form.
	 * Overrides parent method by adding extra {@link taskDb} task before {@link taskSave}.
	 */
	public function taskProcess()
	{
		if($this->form->submitted())
		{
			if($this->form->validate())
			{
				$this->db();
				$this->save();
			}
			$this->form->hasErrors()
				? $this->error()
				: $this->success();
		}
	}
	
	/**
	 * Looks for appropriate DB commands file in current module's 'data' directory
	 * and if the file is there executes all SQL statements from it.
	 * @since 1.1.0
	 */
	public function taskDb($sqlFile=null)
	{
		if(!$sqlFile)
		{
			$sqlFile = $this->module->basePath . DS . 'data' . DS;
			if($this->fromVersion===false)
				$sqlFile.= 'install.mysql.sql';
			elseif($this->toVersion===false)
				$sqlFile.= 'uninstall.mysql.sql';
			else
				$sqlFile.= $this->fromVersion.'-'.$this->toVersion.'.mysql.sql';
		}
		
		if(file_exists($sqlFile))
		{
			$connection = app()->db;
			$transaction = $connection->beginTransaction();
			try
			{
				$dump = explode(';',file_get_contents($sqlFile));
				foreach($dump as $sql)
					if(trim($sql))
						$connection->createCommand($sql)->execute();
				$transaction->commit();
			}
			catch(Exception $e)
			{
				$transaction->rollBack();
				$this->model->addError('attribute', $this->t('Following error occured: {error}', array(
					'{error}' => $e->getMessage()
				)));
				return;
			}
		}
	}
	
	/**
	 * Saves module configuration, authentification roles and attaches filters to other modules.
	 * Adds installation report to install helper and removes itself from installers stack.
	 * Redirects (non-AJAX form submission) or re-initializes 'install.process' worklet.
	 */
	public function taskSuccess()
	{
		$this->show = false;
		
		// saving module configuration
		$configTrace = '';
		$m = $this->module;
		while($m instanceOf UWebModule)
		{
			$configTrace = '["modules"]["'.$m->name.'"]' . $configTrace;
			$m = $m->getParentModule();
		}
		eval('$config' . $configTrace . ' = $this->moduleConfig();');
		UHelper::saveConfig(app()->basePath .DS. 'config' .DS. 'public' .DS. 'modules.php', $config, ($this->toVersion===false));
		
		// saving module version
		unset($config);
		eval('$config' . $configTrace . '["params"]["version"] = "' .$this->toVersion. '";');
		UHelper::saveConfig(app()->basePath .DS. 'config' .DS. 'public' .DS. 'modules.php', $config);
		
		// saving module filters
		foreach($this->moduleFilters() as $m=>$f)
		{
			$configTrace = '';
			$mChain = explode('.',$m);
			foreach($mChain as $elem)
				$configTrace.= '["modules"]["'.$elem.'"]';
			if(is_array($f))
				foreach($f as $k)
					eval('$config' . $configTrace . '["filters"]["' . $k . '"] = '.
						($this->toVersion===false?'null':'true').';');
			else
				eval($configTrace = '$config' . $configTrace . '["filters"]["' . $f . '"] = '.
					($this->toVersion===false?'null':'true').';');
		}
		
		
		$this->module->params = $this->moduleParams();
		UHelper::saveConfig(app()->basePath .DS. 'config' .DS. 'public' .DS. 'modules.php', $config);
		
		// saving module auth roles and associatations
		$auth = $this->moduleAuth();
		$am = app()->authManager;
		if(isset($auth['items']))
		{
			foreach($auth['items'] as $name=>$cfg)
				if($this->toVersion===false)
					$am->removeAuthItem($name);
				elseif($am->getAuthItem($name)===null)
					$am->createAuthItem($name,$cfg[0],$cfg[1],$cfg[2],$cfg[3]);
		}
		if(isset($auth['children']) && $this->toVersion!==false)
		{
			foreach($auth['children'] as $parent=>$kids)
				foreach($kids as $kid)
					if(!$am->hasItemChild($parent,$kid))
						$am->addItemChild($parent,$kid);
		}
		$am->clearAuthAssignments();
		$am->save();
		$am->assign(app()->user->role, app()->user->id);
		
		// clean assets directory is that's an app upgrade
		if($this->module == app() && $this->fromVersion && $this->toVersion)
			app()->file->set(asma()->basePath)->purge();
		
		wm()->get('install.helper')->addReport($this->report());
		wm()->get('install.helper')->removeInstaller($this->id);
		if(app()->request->isAjaxRequest)
			return parent::taskSuccess();
		else
			wm()->addCurrent('install.process');
	}
	
	/**
	 * @return array module default configuration or null if this is an un-installer.
	 */
	public function taskModuleConfig()
	{
		if($this->toVersion === false)
			return null;
		else
			return array('params' => $this->moduleParams());
	}
	
	/**
	 * @return array module default parameters/configuration
	 */
	public function taskModuleParams()
	{
		return array('version' => $this->toVersion);
	}
	
	/**
	 * @return array filters which this module attaches to other modules
	 * <pre>
	 * return array(
	 *     'base' => 'module.filter'
	 * );
	 * </pre>
	 * This means that 'module.filter' will be attached to 'base' module.
	 */
	public function taskModuleFilters()
	{
		return array();
	}
	
	/**
	 * @return array module authentification roles as described in 
	 * {@link http://www.yiiframework.com/doc/guide/topics.auth Yii RBAC}.
	 */
	public function taskModuleAuth()
	{
		return array();
	}
	
	/**
	 * @return installation report.
	 */
	public function report()
	{
		if($this->fromVersion === false)
			$m = '{module} has been successfully installed.';
		elseif($this->toVersion === false)
			$m = '{module} has been successfully un-installed.';
		else
			$m = '{module} has been successfully upgraded to version {version}.';
		return $this->t($m,array('{module}' => $this->moduleTitle,
			'{version}' => $this->toVersion));
	}
	
	/**
	 * @return success URL.
	 */
	public function successUrl()
	{
		return url('/install/process');
	}
	
	/**
	 * @return string previous version towards {@link toVersion}. False if no previous version found.
	 */
	public function identifyPreviousVersion()
	{
		$h = $this->module->getVersionHistory();
		foreach($h as $i=>$v)
			if($v == $this->toVersion)
				return $h[$i-1];
		return false;
	}
}