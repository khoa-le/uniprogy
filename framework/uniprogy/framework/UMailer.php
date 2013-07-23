<?php
/**
 * UMailer class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UMailer is an application component that is used to send emails.
 *
 * @version $Id: UMailer.php 81 2010-12-14 09:26:00 GMT vasiliy.y $
 * @package system
 * @since 1.0.0
 */
class UMailer extends CComponent
{
	private $_mailer = null;
	
	/**
	 * Call mailer method if is is not represented in this component.
	 * @param string method name
	 * @param mixed parameters
	 * @return mixed method execution result
	 */
	public function __call($method, $params)
	{
		return call_user_func_array(array($this->_mailer,$method),$params);
	}
	
	/**
	 * Return mailer property if is is not represented in this component.
	 * @param string parameter name
	 * @return mixed mailer property
	 */
	public function __get($name)
	{
		return $this->_mailer->$name;
	}
	
	/**
	 * Initializes component by creating an instance of EMailer extension
	 * and setting initial property values.
	 */
	public function init()
	{
		$this->_mailer = Yii::createComponent('uniprogy.extensions.mailer.EMailer');
		foreach(app()->params as $k=>$v)
		{
			if(strpos($k,'mail') === 0)
			{
				$key = substr($k,4);
				$this->_mailer->$key = $v;
			}
		}
		$this->_mailer->From = param('systemEmail');
		$this->_mailer->FromName = app()->name;
	}
	
	/**
	 * Sends an email.
	 * @param mixed recipient and sender info. Can be a string with recipient email address,
	 * an active record instance ('email' property must exist in this ar) or an array in the following
	 * format:
	 * <pre>
	 * return array(
	 *     'from' => array('name' => 'John Smith', 'email' => 'john@smith.com'),
	 *     'to' => array('name' => 'Michael Stone', 'email' => 'michael@stone.com'),
	 * );
	 * </pre>
	 * Although 'to' element can also be an instance of active record or an email address string.
	 * @param string email view name
	 * @param array parameters
	 * @param string email layout name
	 * @return boolean whether email has been sent
	 */
	public function send($address, $pattern=null, $params=array(), $layout='email', $language=null)
	{
		$this->prepare($address,$pattern,$params,$layout,$language);
		$result = $this->_mailer->Send();
		$this->reset();
		return $result;
	}
	
	/**
	 * Resets mailer to empty state.
	 */
	public function reset()
	{
		$this->_mailer->ClearAllRecipients();
		$this->_mailer->ClearReplyTos();
		$this->_mailer->ClearAttachments();
		$this->_mailer->ClearCustomHeaders();
		$this->_mailer->Subject = $this->_mailer->Body = $this->_mailer->AltBody = null;
	}
	
	/**
	 * Prepares an email.
	 * @param mixed recipient and sender info.
	 * @param string email view name
	 * @param array parameters
	 * @param string email layout name
	 */
	public function prepare($address, $pattern=null, $params=array(), $layout='email', $language=null)
	{		
		if(is_array($address))
		{
			if(isset($address['from']))
			{
				$this->_mailer->From = $address['from']['email'];
				$this->_mailer->FromName = $address['from']['name'];
			}
			$address = isset($address['to'])?$address['to']:null;
		}
		
		$this->_mailer->AddReplyTo($this->_mailer->From);
		
		if($address instanceOf CActiveRecord)
		{
			$this->_mailer->AddAddress($address->email);
			$address->scenario = 'fullName';
			if(!$language)
				$language = $address->language;
		}
		else
			$this->_mailer->AddAddress($address);
			
		// setting language
		$currentLanguage = app()->language;
		if($language)
			app()->language = $language;
			
		$params['recipient'] = $address;
		$params['site'] = app()->name;
		
		if($pattern)
			$this->render($pattern, $params, $layout);
		else
		{
			$this->_mailer->Subject = isset($params['subject'])?$params['subject']:null;
			if(param('htmlEmails'))
			{
				$this->_mailer->AltBody = isset($params['plainBody'])?$params['plainBody']:null;
				$this->_mailer->Body = isset($params['htmlBody'])?$params['htmlBody']:null;
				if(!isset($params['renderLayout']) || $params['renderLayout'] !== false)
					$this->renderLayout($layout,$params);
			}
			else
				$this->_mailer->Body = isset($params['plainBody'])?$params['plainBody']:null;
		}
		
		// re-setting language
		app()->language = $currentLanguage;
	}
	
	/**
	 * Renders email.
	 * @param string email view name
	 * @param array variables
	 * @param string email layout name
	 */
	public function render($view, $vars, $layout)
	{
		list($viewController,$view) = $this->controller($view);		
		
		$view = '/system/emails/'.$view;
		
		$vars['mailer'] = $this->_mailer;
		$this->_mailer->Body = $viewController->renderPartial($view, $vars, true);
		if($viewController->getViewFile($view.'-plain'))
			$plainBody = $viewController->renderPartial($view.'-plain', $vars, true);
		else
		{
			$plainBody = strip_tags($this->_mailer->Body);
			$plainBody = preg_replace("/\r|\t/","",$plainBody);
			$plainBody = preg_replace("/[\n]{2,}/","\n\n",$plainBody);
		}
		
		if(param('htmlEmails'))
		{
			$this->_mailer->AltBody = $plainBody;
			if(!isset($vars['renderLayout']) || $vars['renderLayout'] !== false)
				$this->renderLayout($layout,$vars);		
		}
		else
			$this->_mailer->Body = $plainBody;
	}
	
	/**
	 * Puts current email body into the layout.
	 * @param string current email body
	 */
	public function renderLayout($layout,$vars)
	{
		list($layoutController,$layout) = $this->controller($layout);
		if ($layout !== null)
		{
			$vars['content'] = $this->_mailer->Body;
			$layout = $layoutController->getLayoutFile($layout);
			$this->_mailer->Body = $layoutController->renderFile($layout, $vars, true);
		}
	}
	
	/**
	 * Creates a {@link UController} instance with an appropriate module assigned to it
	 * depending on the $viewName value.
	 * @param string view name. Pass 'moduleName.viewName' to force mailer to look for a view file
	 * in moduleName module view path.
	 * @return array controller instance and result view name.
	 */
	public function controller($viewName)
	{
		$controller = app()->controller;
		if(strpos($viewName,'/')!==false)
		{
			list($module,$viewName) = explode('/',$viewName);
			$module = UFactory::getModuleFromAlias($module);
			$controller = new UController('mailer', $module);
		}
		return array($controller,$viewName);
	}
}