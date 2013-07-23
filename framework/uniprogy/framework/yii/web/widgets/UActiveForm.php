<?php
/**
 * UActiveForm class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UActiveForm is an extension of {@link http://www.yiiframework.com/doc/api/CActiveForm CActiveForm}.
 *
 * @version $Id: UActiveForm.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.yii.widgets
 * @since 1.0.0
 */
class UActiveForm extends CActiveForm
{	
	/**
	 * @var array collection of form model errors.
	 */
	public static $errorSummaries = array();
	/**
	 * @var boolean whether ajax submission for this form is enabled
	 */
	public $ajax=true;
	
	/**
	 * Initializes the widget.
	 * This renders the form open tag.
	 */
	public function init()
	{
		if(isset($this->htmlOptions['id']))
			$this->id = $this->htmlOptions['id'];
		return parent::init();
	}
	
	/**
	 * Runs the widget.
	 * This registers the necessary javascript code and renders the form close tag.
	 */
	public function run()
	{
		parent::run();
		$id = $this->id;
		if($this->ajax)
			cs()->registerScript(__CLASS__.$id,"\$('#$id').uForm().attach();");
	}
	
	/**
	 * Displays a summary of validation errors for one or several models.
	 * @param mixed the models whose input errors are to be displayed. This can be either
	 * a single model or an array of models.
	 * @param string a piece of HTML code that appears in front of the errors
	 * @param string a piece of HTML code that appears at the end of the errors
	 * @param array additional HTML attributes to be rendered in the container div tag.
	 * @return string the error summary. Empty if no errors are found.
	 */
	public function errorSummary($models,$header=null,$footer=null,$htmlOptions=array())
	{
		if(!isset(self::$errorSummaries[$this->id]))
		{
			$this->enableAjaxValidation = true;
			$eS = parent::errorSummary($models,$header,$footer,$htmlOptions);
			$this->enableAjaxValidation = false;
			self::$errorSummaries[$this->id] = true;
			return $eS;
		}
	}
}