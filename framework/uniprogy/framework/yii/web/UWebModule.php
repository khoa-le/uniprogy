<?php
/**
 * UWebModule class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UWebModule is an extension of {@link http://www.yiiframework.com/doc/api/CWebModule CWebModule}.
 *
 * @version $Id: UWebModule.php 65 2010-10-14 17:55:00 GMT vasiliy.y $
 * @package system.yii.web
 * @since 1.0.0
 */
class UWebModule extends CWebModule
{
	private $_filters=null;
	private $_models=null;
	
	/**
	 * Automatically imports components and models if debugging is enabled
	 * or entire application has not been installed yet.
	 */
	public function preinit()
	{
		if(YII_DEBUG)
			$this->setImport(array($this->getName().'.components.*',$this->getName().'.models.*'));
	}
	
	/**
	 * @return CList filters
	 */
	public function getFilters()
	{
		if($this->_filters!==null)
			return $this->_filters;
		else
		{
			$this->_filters=new CList;
			return $this->_filters;
		}
	}

	/**
	 * @param array filters
	 */
	public function setFilters($value)
	{
		$filters=$this->getFilters();
		foreach($value as $v=>$dummy)
			$filters->add($v);
	}
	
	/**
	 * @return CList models
	 */
	public function getModels()
	{
		if($this->_models!==null)
			return $this->_models;
		else
		{
			$this->_models=new CList;
			return $this->_models;
		}
	}

	/**
	 * @param array models
	 */
	public function setModels($value)
	{
		$models=$this->getModels();
		foreach($value as $v)
			$models->add($v);
	}
	
	/**
	 * @param string patameter name
	 * @return mixed parameter value
	 */
	public function param($name)
	{
		return $this->params[$name];
	}
	
	/**
	 * Shortcut to {@link http://www.yiiframework.com/doc/api/YiiBase#t-detail Yii::t}.
	 * @param string message
	 * @param array parameters
	 * @param string source
	 * @param string language
	 * @return string translated message
	 */
	public function t($message, $params = array(), $source = null, $language = null)
	{
		return t($message, $this->name, $params, $source, $language);
	}
	
	/**
	 * Returns module version. By default it returns application version.
	 * Override this method only if this is non-application module (add-on/custom).
	 * @return string the version of module.
	 * @since 1.1.0
	 */
	public function getVersion()
	{
		return app()->getVersion();
	}
	
	/**
	 * @return string the title of the module
	 * @since 1.1.0
	 */
	public function getTitle()
	{
		return 'Unnamed Module';
	}
	
	/**
	 * @return array modules and their versions which current module requires
	 * <pre>
	 * return array(
	 *     'moduleOne' => '1.0.0',
	 *     'moduleTwo.subModuleOne' => '1.0.0'
	 * );
	 * </pre>
	 * @since 1.1.0
	 */
	public function getRequirements()
	{
		return array();
	}
	
	/**
	 * Checks if module requirements are met.
	 * @return array list of requirements which are not met or boolean true if all requirements are met. 
	 * @param boolean whether to check modules versions strictly or not
	 */
	public function getRequirementsMet($strict=false)
	{
		$notMet = array();
		foreach($this->getRequirements() as $id=>$v)
		{
			if($id == 'app')
				$m = app();
			else
				$m = UFactory::getModuleFromAlias($id);
				
			if(!$m)
				$notMet[$id] = true;
			else
			{
				$mVersion = $strict?$m->param('version'):$m->version;
				if(!$mVersion)
					$notMet[$id] = true;
				elseif(version_compare($mVersion, $v) < 0)
					$notMet[$id] = $v;
			}
		}
		return count($notMet)?$notMet:true;
	}
	
	/**
	 * @return array list of module versions. By default it returns application version history.
	 * Override this method only if this is non-application module (add-on/custom).
	 */
	public function getVersionHistory()
	{
		return app()->getVersionHistory();
	}
	
	/**
	 * Retrieves the named application module.
	 * The module has to be declared in modules. A new instance will be created
	 * when calling this method with the given ID for the first time.
	 * @param string application module ID (case-sensitive)
	 * @return UModule the module instance, null if the module is disabled, does not exist
	 * or one of those modules which this one requires also is disabled, does not exist or is not up to date.
	 */
	public function getModule($id)
	{
		$module = parent::getModule($id);
		if($module && $module->getRequirementsMet()===true)
			return $module;
	}
}