<?php
/**
 * UUserIdentity class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UUserIdentity is an extension of {@link http://www.yiiframework.com/doc/api/CUserIdentity CUserIdentity}.
 * It delegates some of it's methods to the worklet.
 *
 * @version $Id: UUserIdentity.php 78 2010-11-24 10:07:00 GMT vasiliy.y $
 * @package system.yii.web.auth
 * @since 1.0.0
 */
class UUserIdentity extends CUserIdentity
{
	private $_worklet;
	/**
	 * @var string user identity worklet
	 */
	public $worklet = 'base.auth.userIdentity';
	
	/**
	 * Constructor.
	 * @param string username
	 * @param string password
	 */
	public function __construct($username,$password)
	{
		parent::__construct($username,$password);
		$this->init();
	}
	
	/**
	 * Calls the named method which is not a class method by delegating it to the worklet.
	 * Do not call this method. This is a PHP magic method that we override.
	 * @param string the method name
	 * @param array method parameters
	 * @return mixed the method return value
	 */
	public function __call($name,$parameters)
	{
		return call_user_func_array(array($this->_worklet,$name),$parameters);
	}
	
	/**
	 * Initializes component by creating a worklet and calling the parent method.
	 */
	public function init()
	{
		$this->_worklet = wm()->get($this->worklet, array('owner' => $this));
		$this->_worklet->init();
	}
	
	/**
	 * Delegates method to the worklet.
	 */
	public function authenticate()
	{			
		if(($ret = $this->_worklet->authenticate()) !== null)
			return $ret;
		return parent::authentificate();
	}
	
	/**
	 * Delegates method to the worklet.
	 */
	public function getId()
	{
		if(($ret = $this->_worklet->__call('getId',array())) !== null)
			return $ret;
		return parent::getId();
	}
	
	/**
	 * Delegates method to the worklet.
	 */
	public function getName()
	{
		if(($ret = $this->_worklet->__call('getName',array())) !== null)
			return $ret;
		return parent::getName();
	}
}