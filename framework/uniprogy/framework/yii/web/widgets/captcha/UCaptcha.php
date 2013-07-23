<?php
/**
 * UCaptcha class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UCaptcha is an extension of {@link http://www.yiiframework.com/doc/api/CCaptcha CCaptcha}.
 *
 * @version $Id: UCaptcha.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.yii.web.widgets.captcha
 * @since 1.0.0
 */
class UCaptcha extends CCaptcha
{
	/**
	 * @var CModel the data model associated with this widget.
	 */
	public $model;
	/**
	 * @var string the attribute associated with this widget. Starting from version 1.0.9,
	 * the name can contain square brackets (e.g. 'name[1]') which is used to collect tabular data input.
	 */
	public $attribute;
	/**
	 * @var string the ID of the action that should provide CAPTCHA image. Defaults to '/system/captcha',
	 * meaning the 'captcha' action of the 'system' controller. Mode details: {@link http://www.yiiframework.com/doc/api/CCaptcha#captchaAction-detail CCaptcha::captchaAction}.
	 */
	public $captchaAction='/system/captcha';

	/**
	 * Renders the widget.
	 */
	public function run()
	{
		parent::run();
		echo '<div>' . CHtml::activeTextField($this->model, $this->attribute) . '</div>';
	}
}