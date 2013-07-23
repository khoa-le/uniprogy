<?php
class WBaseHelper extends USystemWorklet
{
	private $_keyPrefix;
	
	public function taskSaveToCookie($name,$value,$expire=0)
	{
		$cookie=app()->getRequest()->getCookies()->itemAt($this->getStateKeyPrefix());
		if(!$cookie)
		{
			$cookie = new CHttpCookie($this->getStateKeyPrefix(),'');
			$data = array();
		}
		else
			$data = unserialize($cookie->value);
		$cookie->expire = $expire?time()+$expire:0;
		$data[$name] = $value;
		$cookie->value = serialize($data);
		app()->getRequest()->getCookies()->add($cookie->name,$cookie);
	}
	
	public function taskGetFromCookie($name)
	{
		$cookie=app()->getRequest()->getCookies()->itemAt($this->getStateKeyPrefix());
		if($cookie)
		{
			$data = unserialize($cookie->value);
			return isset($data[$name])?$data[$name]:null;
		}
		return null;
	}
	
	public function getStateKeyPrefix()
	{
		if($this->_keyPrefix!==null)
			return $this->_keyPrefix;
		else
			return $this->_keyPrefix=md5('UniProgy.'.get_class($this).'.'.Yii::app()->getId());
	}
	
	public function setStateKeyPrefix($value)
	{
		$this->_keyPrefix=$value;
	}
}