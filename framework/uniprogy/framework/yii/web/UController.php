<?php
/**
 * UController class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UController is an extension of {@link http://www.yiiframework.com/doc/api/CController CController}.
 *
 * @version $Id: UController.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.yii.web
 * @since 1.0.0
 */
class UController extends CController
{
	/**
	 * @var string the name of the default action class to load when action could not be
	 * located in a normal Yii way
	 */
	public $defaultActionClass = 'UAction';
	
	/**
	 * @var array the breadcrumbs of the current page.
	 */
	public $breadcrumbs=array();
	
	/**
	 * Runs the action overriding default Yii behavior.
	 * @param string action ID
	 */
	public function runUAction($actionID)
	{
		$this->runAction($this->createAction($actionID, true));
	}
	
	/**
	 * Creates the action instance based on the action name.
	 * If action cannot be created via Yii's default behavior it will create an instance of {@link defaultActionClass}.
	 * More details: {@link http://www.yiiframework.com/doc/api/CController#createAction-detail CController::createAction}.
	 * @param string ID of the action. If empty, the {@link defaultAction default action} will be used.
	 * @param boolean whether default Yii behavior needs to be respected
	 * @return CAction the action instance, null if the action does not exist.
	 */
	public function createAction($actionID, $skipDefault=false)
	{
		if($actionID==='')
			$actionID=$this->defaultAction;
			
		if(!$skipDefault && $action = parent::createAction($actionID))
			return $action;
			
		if(strpos($actionID, "/") === false) {
			$baseConfig=array('class'=>$this->defaultActionClass);
			return Yii::createComponent($baseConfig,$this,$actionID);
		}
		return null;
	}
	
	/**
	 * Adds {@link UAccessFilter} to all instances of {@link UController} automatically.
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			array('UAccessFilter'),
		);
	}
	
	/**
	 * @return string the route (module ID, controller ID and action ID) of the current request.
	 * '/default' substrings are cut out of the result.
	 */
	public function getRouteEased()
	{
		return strtr(parent::getRoute(),array('/default'=>''));
	}
	
	/**
	 * Creates and executes a worklet as if it was a widget.
	 * @param string worklet id
	 * @param array worklet configuration
	 * @param boolean whether the rendering result should be returned instead of being echoed
	 * @return string the rendering result. Null if the rendering result is not required.
	 */
	public function worklet($workletID,$config=array(),$return=false)
	{
		$config['space'] = 'runtime';
		$worklet = wm()->get($workletID,$config);
		if($worklet && $worklet->access())
		{		
			$worklet->init();
			if($worklet->show)
				return $worklet->run($return);
		}
	}
	
	/**
	 * Looks for the view file according to the given view name.
	 * More details: {@link http://www.yiiframework.com/doc/api/CController#getViewFile-detail CController::getViewFile}.
	 * In addition to those paths where Yii is trying to find a view file it will also check application view path.
	 * @param string view name
	 * @return string the view file path, false if the view file does not exist
	 */
	public function getViewFile($viewName)
	{
		if($viewFile = parent::getViewFile($viewName))
			return $viewFile;
		if($this->getModule()!==null)
			return $this->resolveViewFile($viewName,app()->getViewPath() . DS . $this->getModule()->name,app()->getViewPath());
	}
	
	/**
	 * Looks for the layout view script based on the layout name.
	 * More details: {@link http://www.yiiframework.com/doc/api/CController#getLayoutFile-detail CController::getLayoutFile}.
	 * In addition to those paths where Yii is trying to find a layout file it will also check application view path.
	 * @param mixed layout name
	 * @return string the view file for the layout. False if the view file cannot be found
	 */
	public function getLayoutFile($layoutName)
	{
		if($layoutName===false)
			return false;
		if($layoutFile = parent::getLayoutFile($layoutName))
			return $layoutFile;
		$layoutName = $this->getLayoutName($layoutName);
		return $this->resolveViewFile($layoutName,app()->getLayoutPath(),app()->getViewPath());
	}
	
	/**
	 * Determines layout name for the current module/controller/action.
	 * @param string layout name
	 * @return string layout name
	 */
	public function getLayoutName($layoutName=null)
	{		
		if(!empty($layoutName))
			return $layoutName;
		elseif(!empty($this->layout))
			$layout = $this->layout;
		else{			
			$module=$this->getModule();
			while($module!==null)
			{
				if($module->layout===false)
					return false;
				if(!empty($module->layout))
					break;
				$module=$module->getParentModule();
			}
			if($module===null)
				$module=Yii::app();
			$layout = $module->layout;
		}
		return $layout;
	}
	
	/**
	 * Forcibly sets controller layout to an appropriate value.
	 * @param CAction action object
	 * @return boolean true
	 */
	protected function beforeAction($action)
	{
		if($this->layout===null)
			$this->layout = app()->theme?app()->theme->resolveControllerLayout($this):'main';
		return true;
	}
	
	/**
	 * A shortcut to {@link UWebModule::t} if this active record belongs to any module
	 * or to {@link http://www.yiiframework.com/doc/api/YiiBase#t-detail Yii::t} otherwise.
	 * @param string message
	 * @param array parameters
	 * @param string source
	 * @param string language
	 * @return string translated message
	 */
	public function t($message, $params = array(), $source = null, $language = null)
	{
		if($this->getModule()!==null)
			return $this->getModule()->t($message, $params, $source, $language);
		else
			return t($message, null, $params, $source, $language);
	}
}