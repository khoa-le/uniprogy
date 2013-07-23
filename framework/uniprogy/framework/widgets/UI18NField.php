<?php
/**
 * UI18NField class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2012 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UI18NField is a widget class that renders same field multiple times for every language so the same content can be entered in multiple languages.
 * 
 * Switching between fields/languages is powered by jQuery UI Tabs.
 *
 * @version $Id: UI18NField.php 1 2011-05-26 10:07:00 GMT vasiliy.y $
 * @package system.widgets
 * @since 1.4.0
 */
class UI18NField extends CInputWidget
{
	/**
	 * @var string field type
	 */
	public $type='text';
	/**
	 * @var array list of languages
	 * array('en_us' => 'English')
	 */
	public $languages=array();
	/**
	 * @var type additional html options
	 */
	public $htmlOptions=array();
	
	/**
	 * Initializes widget.
	 */
	public function init()
	{
		list($this->htmlOptions['name'],$this->htmlOptions['id']) = $this->resolveNameID();
		if(isset($this->languages[app()->sourceLanguage]))
		{
			$tmp = $this->languages[app()->sourceLanguage];
			unset($this->languages[app()->sourceLanguage]);
			$this->languages = array(app()->sourceLanguage => $tmp) + $this->languages;
		}
		return parent::init();
	}
	
	/**
	 * Renders fields and places them inside of jQuery UI Tabs.
	 */
	public function run()
	{
		$tabs = array();
		
		foreach($this->languages as $id=>$name)
			$tabs[$name] = $this->field($id);
		
		$id = 'uTabs_'.$this->htmlOptions['id'].'_'.CHtml::$count++;
		
		$this->widget('zii.widgets.jui.CJuiTabs', array(
			'id' => $id,
		    'tabs' => $tabs,
		    // additional javascript options for the tabs plugin
		    'options'=>array(
		       
		    ),
		));
	}
	
	/**
	 * Renders one instance of a field for a particular language.
	 * @param string language code.
	 * @return string HTML code of the field.
	 */
	public function field($lang)
	{
		$htmlOptions = $this->htmlOptions;
		$htmlOptions['name'].= '['.$lang.']';
		$htmlOptions['id'].= '_'.$lang;
		$htmlOptions['value'] = $this->model->translate($this->attribute,$lang,true);
		switch($this->type)
		{
			case 'text':
				return CHtml::activeTextField($this->model,$this->attribute,$htmlOptions);
				break;
			case 'textarea':
				$this->model->{$this->attribute} = $htmlOptions['value'];
				return CHtml::activeTextArea($this->model,$this->attribute,$htmlOptions);
				break;
			case 'UCKEditor':
				$attributes=array();
				$attributes['model']=$this->model;
				$attributes['attribute']=$this->attribute;
				$attributes['htmlOptions'] = $htmlOptions;
				$this->model->{$this->attribute} = $htmlOptions['value'];
				ob_start();
				app()->controller->widget('UCKEditor', $attributes);
				return ob_get_clean();
				break;
		}
	}
}