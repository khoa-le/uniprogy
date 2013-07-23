<?php
/**
 * UThemeManager class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UThemeManager is an extension of {@link http://www.yiiframework.com/doc/api/CThemeManager CThemeManager}.
 *
 * @version $Id: UThemeManager.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.yii.web
 * @since 1.0.0
 */
class UThemeManager extends CThemeManager
{
	/**
	 * Overrides parent method.
	 * Creates theme class using it's name instead of themeClass property.
	 * @param string name of the theme to be retrieved
	 * @return UTheme the theme retrieved. Null if the theme does not exist.
	 */
	public function getTheme($name)
	{
		$themePath=$this->getBasePath().DIRECTORY_SEPARATOR.$name;
		$themeClassPath=app()->basePath.DS.'components'.DS.ucfirst($name).'Theme.php';
		if(is_dir($themePath) && is_file($themeClassPath))
		{
			$class = ucfirst($name) . 'Theme';
			return new $class($name,$themePath,$this->getBaseUrl().'/'.$name);
		}
		return null;
	}
}