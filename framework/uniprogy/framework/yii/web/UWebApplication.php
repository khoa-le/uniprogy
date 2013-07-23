<?php
/**
 * UWebApplication class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UWebApplication is an extension of {@link http://www.yiiframework.com/doc/api/CWebApplication CWebApplication}.
 *
 * @version $Id: UWebApplication.php 82 2010-12-16 14:02:00 GMT vasiliy.y $
 * @package system.yii.web
 * @since 1.0.0
 */
class UWebApplication extends CWebApplication
{	
	/**
	 * Registers 'uniprogy' path alias and calls parent method.
	 * @param mixed application configuration.
	 */
	public function __construct($config=null)
	{
		Yii::setPathOfAlias('uniprogy',UP_PATH . DS . '..');
		$this->setId(sprintf('%x',crc32(dirname($_SERVER['SCRIPT_FILENAME']))));
		parent::__construct($config);
	}
	
	/**
	 * Initializes the application.
	 * This method overrides the parent implementation:
	 * <ul>
	 *     <li>tries to retrieve session id from $_POST variables</li>
	 *     <li>adds rules defined by initial worklet to url manager</li>
	 * </ul>
	 */
	public function init()
	{
		parent::init();
		
		if(isset($_POST['PHPSESSID']))
			session_id($_POST['PHPSESSID']);			
			
		$rules = wm()->get(wm()->getInitialWorklet())->urlRules();		
		app()->urlManager->rules = $rules;
		app()->urlManager->init();
	}
	
	/**
	 * Overrides parent implementation by trying to create default controller if
	 * at least module has been found. Finally it will check with initial worklet
	 * if there's any special controller creation implementation for such case.
     * @param string the route of the request.
	 * @param UWebModule the module that the new controller will belong to. Defaults to null, meaning the application
	 * instance is the owner.
	 * @return array the controller instance and the action ID. Null if the controller class does not exist or the route is invalid.
	 */
	public function createController($route,$owner=null)
	{
		if($ca = parent::createController($route,$owner))
			return $ca;
		if($owner)
			return parent::createController('default/'.substr($route,0,-1),$owner);
		else
			return wm()->get(wm()->getInitialWorklet())->createController($route,$owner);
	}
	
	/**
	 * @param string locale ID (e.g. en_US).
	 * @return ULocale the locale instance
	 */
	public function getLocale($localeID=null)
	{
		return ULocale::getInstance($localeID===null?$this->getLanguage():$localeID);
	}
	
	/**
	 * @return string the directory that contains the locale data. It defaults to 'framework/i18n/data'.
	 */
	public function getLocaleDataPath()
	{
		return ULocale::$dataPath===null ? Yii::getPathOfAlias('system.i18n.data') : ULocale::$dataPath;
	}
	
	/**
	 * @param string the directory that contains the locale data.
	 */
	public function setLocaleDataPath($value)
	{
		ULocale::$dataPath=$value;
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
	
	/**
	 * Returns a new instance of module class from the specified alias.
	 * It ignores all module params, settings, etc. It just returns the instance.
	 * @param string module alias.
	 * @return UModule module class instance.
	 */
	public function getModuleClass($alias)
	{
		$alias = 'application.modules.'.str_replace('.','.modules.',$alias);
		$name  = substr($alias,strrpos($alias,'.')+1);
		$class = ucfirst($name).'Module';
		Yii::import($alias.'.'.$class);
		if(class_exists($class))
			return new $class($name,app());
	}
	
	/**
	 * @param string patameter name
	 * @return mixed parameter value
	 * @since 1.1.0
	 */
	public function param($name)
	{
		return $this->params[$name];
	}
	
	/**
	 * @return array list of modules which are a part of application
	 */
	public function getAppModules()
	{
		return array(
		);
	}
	
	/**
	 * @param UWebModule or string module alias ID
	 * @return boolean whether module is a part of application or an add-on/custom one
	 */
	public function getIsAppModule($module)
	{
		$id = $module instanceOf UWebModule?UFactory::getModuleAlias($module):$module;
		return in_array($id,$this->getAppModules());
	}
	
	/**
	 * @return array list of application versions
	 * @since 1.1.0
	 */
	public function getVersionHistory()
	{
		return array();
	}
	
	/**
	 * Shortcut to {@link http://www.yiiframework.com/doc/api/YiiBase#t-detail Yii::t}.
	 * @param string message
	 * @param array parameters
	 * @param string source
	 * @param string language
	 * @return string translated message
	 * @since 1.1.0
	 */
	public function t($message, $params = array(), $source = null, $language = null)
	{
		return t($message, null, $params, $source, $language);
	}
	
	/**
	 * Displays the uncaught PHP exception.
	 * This method displays the exception in HTML when there is
	 * no active error handler.
	 * @param Exception $exception the uncaught exception
	 */
	public function displayException($exception)
	{
		if(YII_DEBUG)
		{
			echo get_class($exception)."\n";
			echo $exception->getMessage().' ('.$exception->getFile().':'.$exception->getLine().")\n";
			echo $exception->getTraceAsString();
		}
		else
		{
			echo $exception->getMessage();
		}
	}
}