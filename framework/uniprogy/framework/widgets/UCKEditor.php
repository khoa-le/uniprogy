<?php
/**
 * UCKEditor class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UCKEditor is an input widget class that is used to display WISIWYG editor.
 * {@link http://ckeditor.com/ CKEditor}
 *
 * @version $Id: UCKEditor.php 79 2010-12-10 10:07:00 GMT vasiliy.y $
 * @package system.widgets
 * @since 1.0.0
 */
class UCKEditor extends CInputWidget
{
	private $_editorOptions = array(
		'height' => '400',
		'customConfig' => '',
		'toolbar' => "Full",
	);
	
	/**
	 * Editor options getter.
	 * @return array editor options.
	 */
	public function getEditorOptions()
	{
		return $this->_editorOptions;
	}
	
	/**
	 * Editor options setter.
	 * @param array editor options.
	 */
	public function setEditorOptions($value)
	{
		if(is_array($value))
			foreach($value as $k=>$v)
				$this->_editorOptions[$k] = $v;
	}
	
	/**
	 * Initializes editor.
	 */
	public function init()
	{
		app()->controller->beginWidget('uniprogy.extensions.NHCKEditor.CKEditorWidget',array(
			'model' => $this->model,
			'attribute' => $this->attribute,
			'editorOptions' => $this->editorOptions,
			'htmlOptions' => $this->htmlOptions,
		));
	}
	
	/**
	 * Renders editor.
	 */
	public function run()
	{
		app()->controller->endWidget();
	}
}