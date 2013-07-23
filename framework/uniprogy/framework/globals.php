<?php
/**
 * globals file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * This is a collection of shortcuts or commonly used functions.
 *
 * @version $Id: globals.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system
 * @since 1.0.0
 */

/**
 * This is the shortcut to DIRECTORY_SEPARATOR
 */
defined('DS') or define('DS',DIRECTORY_SEPARATOR);
 
/**
 * This is the shortcut to Yii::app()
 */
function app()
{
    return Yii::app();
}

/**
 * This is the shortcut to Yii::app()->widgets
 */
function widgets()
{
	return Yii::app()->widgets;
}
 
/**
 * This is the shortcut to Yii::app()->clientScript
 */
function cs()
{
    // You could also call the client script instance via Yii::app()->clientScript
    // But this is faster
    return Yii::app()->getClientScript();
}

function asma()
{
	return Yii::app()->getAssetManager();
}
 
/**
 * This is the shortcut to Yii::app()->user.
 */
function user() 
{
    return Yii::app()->getUser();
}
 
/**
 * This is the shortcut to Yii::app()->createUrl()
 */
function url($route,$params=array(),$schema='',$ampersand='&')
{
	$u = Yii::app()->createUrl($route,$params,$schema,$ampersand);
	return $u?$u:aUrl('/');
}

/**
 * This is the shortcut to Yii::app()->getController()->createAbsoluteUrl()
 */
function aUrl($route,$params=array(),$schema='',$ampersand='&')
{
    return Yii::app()->getController()->createAbsoluteUrl($route,$params,$schema,$ampersand);
}
 
/**
 * This is the shortcut to CHtml::encode
 */
function h($text)
{
    return htmlspecialchars($text,ENT_QUOTES,Yii::app()->charset);
}
 
/**
 * This is the shortcut to CHtml::link()
 */
function l($text, $url = '#', $htmlOptions = array()) 
{
    return CHtml::link($text, $url, $htmlOptions);
}
 
/**
 * This is the shortcut to Yii::t() with default category = 'uniprogy'
 */
function t($message, $module=null, $params = array(), $source = null, $language = null) 
{
	if($module === 'yii')
		$category = $module;
	else
		$category = $module === null ? 'uniprogy' : ucfirst($module).'Module.uniprogy';
    return Yii::t($category, $message, $params, $source, $language);
}
 
/**
 * This is the shortcut to Yii::app()->request->baseUrl
 * If the parameter is given, it will be returned and prefixed with the app baseUrl.
 */
function bu($url=null) 
{
    static $baseUrl;
    if ($baseUrl===null)
        $baseUrl=Yii::app()->getRequest()->getBaseUrl();
    return $url===null ? $baseUrl : $baseUrl.'/'.ltrim($url,'/');
}
 
/**
 * Returns the named application parameter.
 * This is the shortcut to Yii::app()->params[$name].
 */
function param($name) 
{
    return Yii::app()->params[$name];
}

/**
 * This is the shortcut to Yii::app()->getModel()
 */
function m($moduleName)
{
	return app()->getModule($moduleName);
}

/**
 * This is the shortcut to Yii::app()->workletManager
 */
function wm()
{
	return Yii::app()->workletManager;
}

/**
 * This is the shortcut to Yii::app()->getLocale()
 */
function locale()
{
	return app()->locale;
}

/**
 * This is the shortcut to Yii::app()->getLocale()->getTextFormatter()
 */
function txt()
{
	return locale()->textFormatter;
}

/**
 * Applies user GMT difference to the timestamp.
 * @param integer source timestamp
 * @param whether to apply GMT difference or un-apply
 * @return integer timestamp
 */
function utime($value,$apply=true)
{
	$userTZ = app()->user->isGuest?app()->param('timeZone'):app()->user->modelData('timeZone');
	return UTimestamp::applyGMT($value,$userTZ,$apply);
}