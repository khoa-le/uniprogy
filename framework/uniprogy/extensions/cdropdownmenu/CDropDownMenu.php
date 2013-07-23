<?php
/**
 * CDropDownMenu class file.
 *
 * @author Herbert Maschke <thyseus@gmail.com>
 * @link http://www.yiiframework.com/
 */

/**
 * CDropDownMenu is an extension to CMenu that supports Drop-Down Menus using the
 * superfish jquery-plugin.
 *
 * Please be sure to also read the CMenu API Documentation to understand how this 
 * menu works.
 *
 */

Yii::import('zii.widgets.CMenu');

class CDropDownMenu extends CMenu
{
	public $cssFile = 'superfish.css';
	public $options;
	public $activeCssClass = 'current';

	public function init()
	{
		parent::init();
	}

	/**
	 * Calls {@link renderMenu} to render the menu.
	 */
	public function run()
	{
		$this->renderDropDownMenu($this->items);
	}

	protected function renderDropDownMenu($items)
	{
		if(!isset($this->htmlOptions['class']))
			$this->htmlOptions['class'] = '';
		$this->htmlOptions['class'].= ' sf-menu';
		$this->renderMenu($items);

		$this->registerClientScript();
		echo '<div style="clear:both;"></div>';
	}

	protected function registerClientScript()
	{
		$dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets';
		$baseUrl = Yii::app()->getAssetManager()->publish($dir);

		Yii::app()->clientScript->registerCoreScript('jquery');
		Yii::app()->clientScript->registerCssFile($baseUrl . '/' . $this->cssFile); 
		Yii::app()->clientScript->registerScriptFile($baseUrl . '/' . 'superfish.js',CClientScript::POS_HEAD);
		Yii::app()->clientScript->registerScriptFile($baseUrl . '/' . 'hoverIntent.js',CClientScript::POS_HEAD);
		Yii::app()->clientScript->registerScript('superfish', '$("ul.sf-menu").superfish('.CJavaScript::encode($this->options).'); ',CClientScript::POS_READY);
	}

}
