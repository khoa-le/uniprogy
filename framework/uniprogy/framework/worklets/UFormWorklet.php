<?php
/**
 * UFormWorklet class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UFormWorklet is the major worklet class that has all functionality required to build and process a form.
 *
 * @version $Id: UFormWorklet.php 73 2010-11-09 09:26:00 GMT vasiliy.y $
 * @package system.worklets
 * @since 1.0.0
 */
class UFormWorklet extends UWidgetWorklet
{
	/**
	 * @var array form properties.
	 * @link http://www.yiiframework.com/doc/api/CForm
	 */
	public $properties=array();
	/**
	 * @var mixed form object.
	 */
	public $form=null;
	/**
	 * @var string URL where to redirect user when form has been successfully processed.
	 */
	public $successUrl=null;
	/**
	 * @var string model class name that will be associated with this form.
	 */
	public $modelClassName=null;
	/**
	 * @var mixed model object.
	 */
	public $model=null;
	/**
	 * @var string primary key for the {@link modelClassName}.
	 */
	public $primaryKey=null;
	/**
	 * @var boolean new record or an update.
	 * @see taskModel
	 */
	public $isNewRecord=false;
	
	/**
	 * Worklet builder.
	 * <ul>
	 * <li>Creates a model.</li>
	 * <li>Prepares form configuration.</li>
	 * <li>Creates a form.</li>
	 * <li>Processes a form.</li>
	 * </ul>
	 */
	public function taskBuild()
	{
		$this->model();
		$this->config();
		$this->createForm();
		$this->process();
	}
	
	/**
	 * Creates a model.
	 * @return CModel model
	 */
	public function taskModel()
	{
		if($this->model===null && $this->modelClassName!==null)
		{
			$id = $this->primaryKey !== null
				? app()->request->getParam($this->primaryKey,null)
				: null;
				
			if($id)
				$this->model = CActiveRecord::model($this->modelClassName)->findByPk($id);
				
			if(!$this->model)
				$this->model = new $this->modelClassName;
				
			if($this->model instanceOf CActiveRecord)
				$this->isNewRecord = $this->model->isNewRecord;
		}
		return $this->model;
	}
	
	/**
	 * Prepares form configuration: properties, success URL.
	 */
	public function taskConfig()
	{
		$this->properties = CMap::mergeArray($this->properties, $this->properties());
		if(!isset($this->successUrl))
			$this->successUrl = $this->successUrl();
		parent::taskConfig();
	}
	
	/**
	 * Creates a form.
	 */
	public function taskCreateForm()
	{
		if($this->form === null) {
			$this->form = new UForm($this->properties);
			if($this->form->getId() === null)
				$this->form->setId($this->getFormId());
			$this->form->loadData();
		}
	}
	
	/**
	 * Processes a form.
	 * If form has been submitted it is then validated and worklets attemps to save new data.
	 * If there are no errors it will execute {@link taskSuccess} or {@link taskError} otherwise.
	 */
	public function taskProcess()
	{
		if($this->form->submitted())
		{
			if($this->form->validate())
				$this->save();
			$this->form->hasErrors()
				? $this->error()
				: $this->success();
		}
	}
	
	/**
	 * Renders the form.
	 */
	public function taskRenderOutput()
	{
		$this->render('form');
	}
	
	/**
	 * Renders the open tag of the {@link form} using {@link http://www.yiiframework.com/doc/api/1.1/CForm#renderBegin-detail CForm::renderBegin}.
	 */
	public function taskRenderBegin()
	{
		echo $this->form->renderBegin();
	}
	
	/**
	 * Renders the body and closing tag of the {@link form} using {@link http://www.yiiframework.com/doc/api/1.1/CForm#renderBody-detail CForm::renderBody}
	 * and {@link http://www.yiiframework.com/doc/api/1.1/CForm#renderEnd-detail CForm::renderEnd}.
	 */
	public function taskRenderEnd()
	{
		echo $this->form->renderBody() . $this->form->renderEnd();
	}
	
	/**
	 * Goes through all models of the form and saves every one that is an instance of CActiveRecord.
	 */
	public function taskSave()
	{
		$models = $this->form->getModels();
		foreach($models as $model)
		{
			if($model instanceOf CActiveRecord)
				$model->save(false);
		}
	}
	
	/**
	 * Error task is executed when form processing has errors.
	 */
	public function taskError()
	{
		if(app()->request->isAjaxRequest || app()->request->isActionScriptRequest)
			$this->ajaxError();
		else
			$this->htmlError();
	}
	
	/**
	 * Success task is executed when form processing is successful.
	 */
	public function taskSuccess()
	{
		if(app()->request->isAjaxRequest || app()->request->isActionScriptRequest)
			$this->ajaxSuccess();
		else
			$this->htmlSuccess();
	}
	
	/**
	 * @return array form properties according to {@link http://www.yiiframework.com/doc/api/CForm}.
	 */
	public function properties()
	{
		return array();
	}
	
	/**
	 * @return string URL where user will be redirected after successful form processing.
	 */
	public function successUrl()
	{
		return url('/');
	}
	
	/**
	 * This method is called by {@link taskSuccess} task if form has been submitted via HTML (non-AJAX).
	 * Default behavior: redirect to {@link successUrl}.
	 */
	public function htmlSuccess()
	{
		app()->request->redirect($this->successUrl);
	}
	
	/**
	 * This method is called by {@link taskSuccess} task if form has been submitted via AJAX.
	 * Default behavior: add JSON command to redirect to {@link successUrl}.
	 */
	public function ajaxSuccess()
	{
		wm()->get('base.init')->addToJson(array('redirect' => $this->successUrl));
	}
	
	/**
	 * This method is called by {@link taskError} task if form has been submitted via HTML (non-AJAX).
	 * Default behavior: do nothing because form render will display error summary anyway.
	 */
	public function htmlError() {}
	
	/**
	 * This method is called by {@link taskError} task if form has been submitted via AJAX.
	 * Default behavior: add JSON command to display form errors.
	 */
	public function ajaxError()
	{
		wm()->get('base.init')->addToJson(array('errors' => $this->form->errorSummaryAsArray()));
	}
	
	/**
	 * Returns nested elements array as a reference.
	 * 
	 * Ex.: if form has nested elements structure like this:
	 * <pre>
	 * array(
	 *     'elements' => array(
	 *         'subForm' => array(
	 *             'type' => 'UForm',
	 *             'elements' => array(
	 *                 ...
	 *             )
	 *         )
	 *     )
	 * )
	 * </pre>
	 * if we call:
	 * <pre>$elements =& $worklet->getElements(array('subForm'));</pre>
	 * $elements will be a refernce to $worklet->properties['elements']['subForm']['elements'].
	 * 
	 * @param array elements trace.
	 * @return array elements array.
	 */
	public function &getElements($key)
	{
		$elements =& $this->properties['elements'];
		if(is_array($key))
		{
			while(($k = current($key))!==false)
			{
				if(isset($elements[$k]) && isset($elements[$k]['elements']))
				{
					$elements =& $elements[$k]['elements'];
					next($key);
				}
				else
					break;
			}
		}
		return $elements;
	}
	
	/**
	 * Inserts items before specified index into the form elements array.
	 *
	 * @param string element index.
	 * @param array new elements to add.
	 * @param mixed elements trace
	 * @return boolean true
	 * @see getElements
	 */
	public function insertBefore($index, $items, $elementsKey=null)
	{
		$elements =& $this->getElements($elementsKey);
		$keys = array_keys($elements);
		$key = array_search($index,$keys,true);
		if($key === false || $key === 0)
			return $this->insert('top', $items, $elementsKey);
			
		$elements = array_slice($elements,0,$key)
			+ $items + array_slice($elements,$key);
		return true;
	}
	
	/**
	 * Inserts items after specified index into the form elements array.
	 *
	 * @param string element index.
	 * @param array new elements to add.
	 * @param mixed elements trace
	 * @return boolean true
	 * @see getElements
	 */
	public function insertAfter($index, $items, $elementsKey=null)
	{
		$elements =& $this->getElements($elementsKey);
		$keys = array_keys($elements);
		$key = array_search($index,$keys,true);
		if($key === false || $key == count($keys)-1)
			return $this->insert('bottom', $items, $elementsKey);
			
		$key++;
		$elements = array_slice($elements,0,$key)
			+ $items + array_slice($elements,$key);
		return true;
	}
	
	/**
	 * Inserts items on top or bottom of the form elements array.
	 *
	 * @param string position 'top' or 'bottom'.
	 * @param array new elements to add.
	 * @param mixed elements trace
	 * @return boolean true
	 * @see getElements
	 */
	public function insert($position, $items, $elementsKey=null)
	{
		$elements =& $this->getElements($elementsKey);
		if($position == 'top')
			$elements = $items + $elements;
		elseif($position == 'bottom')
			$elements = $elements + $items;
		return true;
	}
	
	/**
	 * Merges form properties.
	 * @param array properties to merge with.
	 */
	public function merge($properties)
	{
		$this->properties = CMap::mergeArray($this->properties, $properties);
	}
	
	/**
	 * @return string form id.
	 */
	public function getFormId()
	{
		return 'uForm_' . UHelper::camelize($this->getId());
	}
}