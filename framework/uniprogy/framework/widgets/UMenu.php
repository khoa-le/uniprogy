<?php
/**
 * UMenu class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UMenu is an extension of {@link http://www.yiiframework.com/doc/api/CMenu CMenu}.
 *
 * @version $Id: UMenu.php 1 2011-10-30 10:47:00 GMT vasiliy.y $
 * @package system.widgets
 * @since 1.1.9
 */
Yii::import('zii.widgets.CMenu', true);
class UMenu extends CMenu
{
	/**
	 * Overriding default initializing by modifying $route variable.
	 */
	public function init()
	{
		$this->htmlOptions['id']=$this->getId();
		$route=$this->getController()->getRouteEased();
		$this->items=$this->normalizeItems($this->items,$route,$hasActiveChild);
	}
}