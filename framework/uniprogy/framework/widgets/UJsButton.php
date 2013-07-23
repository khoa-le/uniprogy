<?php
/**
 * UJsButton class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UJsButton is a widget class that renders JavaScript button and binds click event to it.
 *
 * @version $Id: UJsButton.php 81 2010-12-14 10:07:00 GMT vasiliy.y $
 * @package system.widgets
 * @since 1.0.0
 */
class UJsButton extends CWidget
{
	/**
	 * @var string the attribute associated with this widget.
	 */
	public $attribute;
	/**
	 * @var CModel the data model associated with this widget.
	 */
	public $model;
	/**
	 * @var string the input name. This must be set if {@link model} is not set.
	 */
	public $name;
	/**
	 * @var string button label.
	 */
	public $label;
	/**
	 * @var string JavaScript function which will be bind to the click event.
	 */
	public $callback;
	/**
	 * @var string button ID/name prefix.
	 */
	public $prefix = 'upb_';
	/**
	 * @var array additional HTML options to be rendered in the input tag.
	 */
	public $htmlOptions = array();
	
	private static $_count = 0;

	/**
	 * @return string the ID of the button.
	 */
	protected function resolveID()
	{
		return $this->prefix . self::$_count++;
	}
	
	/**
	 * Renders JS button and binds click event to it.
	 */
	public function run()
	{
		if(!isset($this->htmlOptions['id']))
		{
			$this->htmlOptions['name'] = $this->htmlOptions['id'] = $this->resolveID() . md5($this->label);
		}
			
		echo CHtml::button($this->label, $this->htmlOptions);
		cs()->registerScript($this->htmlOptions['id'],
			'jQuery("#' . $this->htmlOptions['id'] . '").click(function(e){' . $this->callback . '; e.preventDefault()});');
	}
}