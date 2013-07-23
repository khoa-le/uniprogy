<?php
/**
 * UForm class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UForm is an extension of {@link http://www.yiiframework.com/doc/api/CForm CForm}.
 *
 * @version $Id: UForm.php 79 2010-12-10 14:02:00 GMT vasiliy.y $
 * @package system.yii.web.form
 * @since 1.0.0
 */
class UForm extends CForm
{
	/**
	 * @var string the name of the class for representing a form input element. Defaults to 'CFormInputElement'.
	 */
	public $inputElementClass='UFormInputElement';
	/**
	 * @var boolean whether to show error summary. Defaults to false.
	 */
	public $showErrorSummary=true;
	/**
	 * @var array the configuration used to create the active form widget.
	 * The widget will be used to render the form tag and the error messages.
	 * The 'class' option is required, which specifies the class of the widget.
	 * The rest of the options will be passed to {@link http://www.yiiframework.com/doc/api/CBaseController#beginWidget CBaseController::beginWidget} call.
	 * Defaults to array('class'=>'UActiveForm').
	 */
	public $activeForm=array(
		'class'=>'UActiveForm'
	);
	
	/**
	 * Initializes this form.
	 * This method is invoked at the end of the constructor.
	 * You may override this method to provide customized initialization (such as
	 * configuring the form object).
	 */
	public function init()
	{
		CHtml::$errorCss = '';
		CHtml::$beforeRequiredLabel = '<span class="required">*</span>';
		CHtml::$afterRequiredLabel = '';
	}
	
	/**
	 * @return boolean whether any of form models has errors
	 */
	public function hasErrors()
	{
		$models = $this->getModels();
		foreach($models as $m) {
			if($m->hasErrors())
				return true;
		}
		return false;
	}
	
	/**
	 * @return string form id
	 */
	public function getId()
	{
		return isset($this->attributes['id'])
			? $this->attributes['id']
			: null;
	}
	
	/**
	 * @param string form id
	 */
	public function setId($value)
	{
		$this->attributes['id'] = $value;
	}
	
	/**
	 * Overrides original method: we don't want to check if button has been clicked.
	 * Sometimes during AJAX requests button isn't clicked at all.
	 * @param string the name of the submit button
	 * @param boolean whether to call loadData if the form is submitted so that
	 * the submitted data can be populated to the associated models.
	 * @return boolean whether this form is submitted.
	 */
	public function submitted($buttonName='submit',$loadData=true)
	{
		$ret=$this->clicked($this->getUniqueId());
		if($ret && $loadData)
			$this->loadData();
		return $ret;
	}
	
	/**
	 * Submits a form.
	 * @since 1.1.2
	 */
	public function submit()
	{
		$_POST[$this->getUniqueId()] = '1';
	}
	
	/**
	 * Returns errors from all form models as array.
	 * @return array form models' errors
	 */
	public function errorSummaryAsArray()
	{
		$models = $this->getModels();
		$data = array();
		foreach($models as $m) {
			foreach($m->getErrors() as $attr => $errors)
			{
				foreach($errors as $error)
				{
					$opts = isset($this->elements[$attr]->htmlOptions)
						? $this->elements[$attr]->htmlOptions
						: array();
						
					CHtml::resolveNameID($m, $attr, $opts);
					$data[] = array('element' => $opts['name'], 'message' => $error);
				}
			}
		}
		return $data;
	}
}