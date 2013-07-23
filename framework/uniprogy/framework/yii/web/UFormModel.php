<?php
/**
 * UFormModel class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UFormModel is an extension of {@link http://www.yiiframework.com/doc/api/CFormModel CFormModel}.
 *
 * @version $Id: UFormModel.php 65 2010-10-14 07:18:00 GMT vasiliy.y $
 * @package system.yii.web
 * @since 1.0.0
 */
class UFormModel extends CFormModel
{
	private $_module;
	
	/**
	 * For all UFormModel's you must specify module, which it belongs to.
	 * <pre>return 'base';</pre>
	 */
	public static function module()
	{
	}
	
	/**
	 * Module getter.
	 * @return UModule module instance
	 */
	public function getModule()
	{
		if(!isset($this->_module))
		{
			if($this->module()!==null)
				$this->_module = UFactory::getModuleFromAlias($this->module());
			else
				$this->_module = null;
		}
		return $this->_module;
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
			return $this->getModule()->t($message,$params,$source,$language);
		else
			return t($message, null, $params, $source, $language);
	}
	
	/**
	 * Constructor.
	 * If this model belongs to any module script will check
	 * if any additional behaviors need to be applied.
	 * @param string scenario
	 */
	public function __construct($scenario='insert')
	{
		if($this->getModule()!==null)
			UModelConstructor::process($this);
		return parent::__construct($scenario);
	}
	
	/**
	 * Attaches behavior to the model.
	 * @param string behavior name
	 * @param mixed behavior instance or id
	 * @return mixed behavior instance or boolean false
	 */
	public function attachBehavior($name,$behavior)
	{
		if(!$behavior instanceof IBehavior)
		{
			$class = null;
			$config = array();
			if(!is_array($behavior))
				$class = $behavior;
			else
			{
				$class = $name;
				$config = $behavior;
			}
				
			if(strpos($class,'.')!==false)
				$behavior = UFactory::getBehavior($class,$config);
		}
		return parent::attachBehavior($name,$behavior);
	}
}