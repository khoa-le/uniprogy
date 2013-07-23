<?php
/**
 * UUploadField class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UUploadField is an input widget class that renders an upload field.
 *
 * @version $Id: UUploadField.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.widgets
 * @since 1.0.0
 */
class UUploadField extends CInputWidget
{
	/**
	 * @var string URL of the script which handles upload.
	 */
	public $url;
	/**
	 * @var string button label.
	 */
	public $label;
	/**
	 * @var string initial content to be pushed above the field, such as image or a link to download a file.
	 */
	public $content;
	
	/**
	 * Initializes the widget.
	 * Adds "base.dialog" worklet to the stack.
	 */
	public function init()
	{
		wm()->add('base.dialog');
		return parent::init();
	}
	
	/**
	 * Renders upload field.
	 */
	public function run()
	{
		$id = 'uUploadField_'.CHtml::$count++;
		$this->htmlOptions['id'] = $id;
		cs()->registerScriptFile(asma()->publish(dirname(__FILE__).'/assets/jquery.uniprogy.upload.js'));		
		echo CHtml::activeHiddenField($this->model,$this->attribute);		
		if($this->content)
		{
			$fieldId = CHtml::getIdByName(CHtml::activeName($this->model,$this->attribute));
			cs()->registerScript($id.'#content',
				'jQuery("#'.$fieldId.'").uUploadField().pushContent("'.CJavaScript::quote($this->content).'");');
		}
		echo CHtml::button($this->label, $this->htmlOptions);
		cs()->registerScript($this->htmlOptions['id'],
			'jQuery("#' . $this->htmlOptions['id'] . '").click(function(e){$.uniprogy.dialog("'.$this->url.'"); e.preventDefault()});');
	}
}