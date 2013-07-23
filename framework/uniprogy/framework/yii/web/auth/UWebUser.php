<?php
/**
 * UWebUser class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UWebUser is an extension of {@link http://www.yiiframework.com/doc/api/CWebUser CWebUser}.
 * It delegates some of it's methods to the worklet.
 *
 * @version $Id: UWebUser.php 79 2010-12-10 10:07:00 GMT vasiliy.y $
 * @package system.yii.web.auth
 * @since 1.0.0
 */
class UWebUser extends CWebUser
{
	private $_worklet;
	/**
	 * @var string web user worklet
	 */
	public $worklet = 'base.auth.webUser';
	
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
		parent::init();
	}
	
	/**
	 * Delegates method to the worklet.
	 */
	public function login($identity,$duration=0)
	{
		if(($ret = $this->_worklet->login($identity,$duration)) !== null)
			return $ret;
                
                //Save cookie to handle cache nginx
                Yii::app()->request->cookies['logged_in'] = new CHttpCookie('logged_in', 1);
                
		return parent::login($identity,$duration);
	}

	/**
	 * Delegates method to the worklet.
	 */
	public function logout($destroySession=true)
	{
		if(($ret = $this->_worklet->logout($destroySession)) !== null)
			return $ret;
                unset(Yii::app()->request->cookies['logged_in']);
		return parent::logout($destroySession);
	}
	
	/**
	 * Delegates method to the worklet.
	 */
	public function loginRequired()
	{
		if(($ret = $this->_worklet->loginRequired()) !== null)
			return $ret;
		return parent::loginRequired();
	}

	/**
	 * Delegates method to the worklet.
	 */
	public function restoreFromCookie()
	{
		if(($ret = $this->_worklet->restoreFromCookie()) !== null)
			return $ret;
		return parent::restoreFromCookie();
	}

	/**
	 * Delegates method to the worklet.
	 */
	public function saveToCookie($duration)
	{
		if(($ret = $this->_worklet->saveToCookie()) !== null)
			return $ret;
		return parent::saveToCookie($duration);
	}

	/**
	 * Delegates method to the worklet.
	 */
	public function createIdentityCookie($name)
	{
		if(($ret = $this->_worklet->createIdentityCookie($name)) !== null)
			return $ret;
		return parent::createIdentityCookie($name);
	}

	/**
	 * Delegates method to the worklet.
	 */
	public function changeIdentity($id,$name,$states)
	{
		if(($ret = $this->_worklet->changeIdentity($id,$name,$states)) !== null)
			return $ret;
		return parent::changeIdentity($id,$name,$states);
	}
	
	/**
	 * Delegates method to the worklet.
	 */
	public function getRole()
	{
		return $this->_worklet->getRole();
	}
}