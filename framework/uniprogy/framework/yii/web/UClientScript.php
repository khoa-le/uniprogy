<?php
/**
 * UClientScript class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UClientScript is an extension of {@link http://www.yiiframework.com/doc/api/CClientScript CClientScript}.
 *
 * @version $Id: UClientScript.php 42 2010-10-01 13:27:00 GMT vasiliy.y $
 * @package system.yii.web
 * @since 1.0.0
 */
class UClientScript extends CClientScript
{
	/**
	 * Registers a CSS file if it is not registered in a session yet.
	 * Since IE doesn't support dynamic CSS files loading we have to pass CSS as text.
	 * @param string URL of the CSS file
	 * @param string media that the CSS file should be applied to. If empty, it means all media types.
	 * @see isFileRegisteredInSession
	 */
	public function registerCssFile($url,$media='')
	{
		if($this->isFileRegisteredInSession($url))
			return;
		// IE doesn't support dynamic CSS files loading, so we need to pass CSS as text
		if(app()->request->isAjaxRequest && (strpos(app()->request->userAgent,'MSIE')!==false)) {
			$am = app()->getAssetManager();
			$cssFile = strtr($url,array($am->baseUrl=>$am->basePath, '/'=>DS));
			$cssContent = @file_get_contents($cssFile);
			return $this->registerCss($url,$cssContent,$media);
		}
		return parent::registerCssFile($url,$media);
	}
	
	/**
	 * Script getter.
	 * @param string script id
	 * @param integer the position of the JavaScript code. Valid values include the following:
	 * <ul>
	 * <li>UClientScript::POS_HEAD : the script is inserted in the head section right before the title element.</li>
	 * <li>UClientScript::POS_BEGIN : the script is inserted at the beginning of the body section.</li>
	 * <li>UClientScript::POS_END : the script is inserted at the end of the body section.</li>
	 * </ul> 
	 * @return string script URL
	 */
	public function script($id,$position=self::POS_READY)
	{
		if($this->isScriptRegistered($id,$position))
			return $this->scripts[$position][$id];
	}
	
	/**
	 * Registers a javascript file only if it is not registered in a session yet.
	 * @param string URL of the javascript file
	 * @param integer the position of the JavaScript code. Valid values include the following:
	 * <ul>
	 * <li>UClientScript::POS_HEAD : the script is inserted in the head section right before the title element.</li>
	 * <li>UClientScript::POS_BEGIN : the script is inserted at the beginning of the body section.</li>
	 * <li>UClientScript::POS_END : the script is inserted at the end of the body section.</li>
	 * </ul>
	 * @see isFileRegisteredInSession
	 */
	public function registerScriptFile($url,$position=self::POS_HEAD)
	{
		if(!$this->isFileRegisteredInSession($url))
		{
			$name=basename($url);
			reset($this->scriptFiles);
			foreach($this->scriptFiles as $pos=>$files)
				foreach($files as $f)
					if(basename($f) == $name && $position != $pos)
						return;
			return parent::registerScriptFile($url,$position);
		}
	}
	
	/**
	 * Registers a core javascript library only if it is not registered in a session yet.
	 * @param string the core javascript library name
	 * @see isFileRegisteredInSession
	 */
	public function registerCoreScript($name)
	{
		if(!$this->isFileRegisteredInSession('core:'.$name))
			return parent::registerCoreScript($name);
	}
	
	/**
	 * Checks if file is registered in a session.
	 * If this is not an ajax request it will always return 'false' and will register a file in a session.
	 * @param string file URL
	 * @return boolean whether file is registered in a session
	 */
	public function isFileRegisteredInSession($url)
	{
		$ajaxRequest = app()->request->isAjaxRequest;
		$session = app()->session['UClientScript'];
		$referrer = app()->request->urlReferrer;
		if($ajaxRequest && isset($session[$referrer][$url]))
			return true;
		if(!$ajaxRequest)
		{		
			$currUrl = app()->request->hostInfo.app()->request->requestUri;
			$session[$currUrl][$url] = true;
			app()->session['UClientScript'] = $session;
		}
		return false;
	}
	
	/**
	 * Overrides {@link http://www.yiiframework.com/doc/api/CClientScript CClientScript::remapScripts}
	 * by unregistering scripts which were already loaded in a session.
	 * @see isFileRegisteredInSession
	 * @since 1.1.0
	 */
	public function remapScripts()
	{
		parent::remapScripts();
		foreach($this->cssFiles as $url=>$media)
			if($this->isFileRegisteredInSession($url))
				unset($this->cssFiles[$url]);
		foreach($this->scriptFiles as $pos=>$scripts)
		{
			if(is_array($scripts))
			{
				foreach($scripts as $key=>$url)
					if($this->isFileRegisteredInSession($url))
						unset($this->scriptFiles[$pos][$key]);
			}
		}
	}
}