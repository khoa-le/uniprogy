<?php
/**
 * UFormInputElement class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UFormInputElement is an extension of {@link http://www.yiiframework.com/doc/api/CFormInputElement CFormInputElement}.
 *
 * @version $Id: UFormInputElement.php 79 2010-12-10 10:07:00 GMT vasiliy.y $
 * @package system.yii.web.form
 * @since 1.0.0
 */
class UFormInputElement extends CFormInputElement
{
	/**
	 * @var string the layout used to render label, input, hint and error. They correspond to the placeholders
	 * "{label}", "{input}", "{hint}" and "{error}".
	 */
	public $layout="{label}\n{input}\n{hint}";
	
	/**
	 * @var string appends to the label
	 */
	public $afterLabel=":";
	/**
	 * @var string prepends the label
	 */
	public $beforeLabel="";
	
	/**
	 * Overriding parent method by surrounding label with {@link beforeLabel} and {@link afterLabel}.
	 * @return string label
	 */
	public function getLabel()
	{
		return $this->beforeLabel.parent::getLabel().$this->afterLabel;
	}
}