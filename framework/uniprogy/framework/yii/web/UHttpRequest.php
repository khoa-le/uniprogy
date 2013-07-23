<?php
/**
 * UHttpRequest class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UHttpRequest is an extension of {@link http://www.yiiframework.com/doc/api/CHttpRequest CHttpRequest}.
 *
 * @version $Id: UHttpRequest.php 79 2010-12-10 15:09:00 GMT vasiliy.y $
 * @package system.yii.web
 * @since 1.0.0
 */
class UHttpRequest extends CHttpRequest
{
	/**
	 * @return boolean whether this is a request received from flash action script.
	 */
	public function getIsActionScriptRequest()
	{
		return isset($_SERVER['HTTP_USER_AGENT'])
			? strpos($_SERVER['HTTP_USER_AGENT'],'Shockwave Flash')!==false
				|| strpos($_SERVER['HTTP_USER_AGENT'],'Adobe Flash')!==false
			: false;
	}
	
	/**
	 * @return boolean whether the request has been received from a mobile browser
	 */
	public function getIsMobile()
	{
		if(isset($_SERVER['HTTP_USER_AGENT']))
		{
			Yii::import('uniprogy.extensions.mobiledetect.Mobile_Detect');
			$d = new Mobile_Detect;
			return $d->isMobile();
		}
		return false;
	}
	
	/**
	 * @return string user IP address
	 */
	public function getUserHostAddress()
	{
		// list of possible server variables that can contain
		// user IP address in the order of priority
		$list = array(
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'REMOTE_ADDR'
		);
		
		foreach($list as $src)
			if(isset($_SERVER[$src]) && !empty($_SERVER[$src]))
				return $_SERVER[$src];
				
		return '127.0.0.1';
	}
}