<?php
require(dirname(__FILE__).DS.'CURL.php');
class CCurl extends CApplicationComponent
{
	private $_curl;
	
	public function __construct()
	{
		$this->_curl = new CURL();
	}
	
	public function __call($method, $params)
	{
		if (is_object($this->_curl)) return call_user_func_array(array($this->_curl, $method), $params);
		else throw new CException(Yii::t('CCurl', 'Can not call a method of a non existent object.'));
	}
}