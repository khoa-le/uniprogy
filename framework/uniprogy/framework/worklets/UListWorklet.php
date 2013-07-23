<?php
/**
 * UListWorklet class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UListWorklet is a worklet class that renders a grid or normal list.
 *
 * @version $Id: UListWorklet.php 82 2010-12-16 13:56:00 GMT vasiliy.y $
 * @package system.worklets
 * @since 1.0.0
 */
class UListWorklet extends UWidgetWorklet
{
	/**
	 * @var CModel model object.
	 */
	public $model;
	/**
	 * @var string model class name.
	 */
	public $modelClassName;
	/**
	 * @var boolean automatically add checkbox to every row.
	 */
	public $addCheckBoxColumn=true;
	/**
	 * @var boolean automatically add update/delete buttons to every row.
	 */
	public $addButtonColumn=true;
	/**
	 * @var boolean automatically add mass buttons - the ones that submit whole list as a form.
	 */
	public $addMassButton=true;
	/**
	 * @var array {@link http://www.yiiframework.com/doc/api/CGridView grid view}
	 * or {@link http://www.yiiframework.com/doc/api/CListView list view} options.
	 */
	public $options=array();
	/**
	 * @var string grid or list
	 */
	public $type='grid';
	
	/**
	 * Creates a model.
	 * Initializes form worklet (if found).
	 */
	public function taskConfig()
	{
		if($this->modelClassName)
		{
			$this->model = new $this->modelClassName('search');
			$this->model->unsetAttributes();
			if(isset($_GET[$this->modelClassName]))
				$this->model->attributes = $_GET[$this->modelClassName];
		}
		$form = $this->form();
		if(is_string($form))
		{
			$w = wm()->get($form);
			$w->init();
		}
		return parent::taskConfig();
	}
	
	/**
	 * @return array settings for a form that will surround the list.
	 */
	public function form()
	{
		if($this->type == 'list')
			return false;
		
		return array(
			'action' => '',
			'method' => 'post',
			'htmlOptions' => array()
		);
	}
	
	/**
	 * @return array columns in {@link http://www.yiiframework.com/doc/api/CGridView#columns-detail CGrivView} format.
	 */
	public function columns()
	{
		return array();
	}
	
	/**
	 * @return array custom mass buttons.
	 */
	public function buttons()
	{
		return array();
	}
	
	/**
	 * @return mixed filter for a grid view.
	 */
	public function filter()
	{
		return $this->model;
	}
	
	/**
	 * @return mixed data provider for a grid view.
	 */
	public function dataProvider()
	{
		return $this->model->search();
	}
	
	/**
	 * @return string item view as descirbed in {@link http://www.yiiframework.com/doc/api/CListView#itemView-detail CListView::itemView}.
	 */
	public function itemView()
	{
		return 'item';
	}
	
	/**
	 * Task that combines all columns data into a single array.
	 * @return array columns in {@link http://www.yiiframework.com/doc/api/CGridView#columns-detail CGrivView} format.
	 */
	public function taskGetColumns()
	{
		if($this->addCheckBoxColumn)
			$columns = CMap::mergeArray(array(array('class'=>'CCheckBoxColumn','name'=>'id','id'=>'id')),
				$this->columns());
		else
			$columns = $this->columns();
			
		if($this->addButtonColumn)
		{
			if(!isset($columns['buttons']))
				$columns['buttons'] = array('class' => 'CButtonColumn');
			if(!isset($columns['buttons']['template']))
				$columns['buttons']['template'] = '{update} {delete}';
			if(!isset($columns['buttons']['updateButtonUrl']))
				$columns['buttons']['updateButtonUrl'] = 'url("'.$this->getParentPath().'/update",array("id"=>$data->primaryKey))';
			if(!isset($columns['buttons']['deleteButtonUrl']))
				$columns['buttons']['deleteButtonUrl'] = 'url("'.$this->getParentPath().'/delete",array("id"=>$data->primaryKey))';
		}
		return $columns;
	}
	
	/**
	 * Task that combines all mass buttons into a single array.
	 * @return array buttons.
	 */
	public function taskGetButtons()
	{
		if($this->addMassButton)
		{
			return CMap::mergeArray(array(
				CHtml::ajaxSubmitButton($this->t('Delete'), url($this->getParentPath().'/delete'), array(
					'success' => 'function(){$.fn.yiiGridView.update("' .$this->getDOMId(). '-grid");}',
				))), $this->buttons());
		}
		return $this->buttons();
	}
	
	/**
	 * Renders a worklet using {@link http://www.yiiframework.com/doc/api/CGridView CGridView}
	 * or {@link http://www.yiiframework.com/doc/api/CListView CListView} widget. Depends on
	 * {@link type}.
	 */
	public function taskRenderOutput()
	{
		if($this->type == 'grid')
		{
			$columns = $this->getColumns();
			$buttons = $this->getButtons();
			
			$options = array(
				'id' => $this->getDOMId().'-grid',
				'dataProvider'=>$this->dataProvider(),
				'filter'=>$this->filter(),
				'selectableRows'=>2,
				'columns'=>$columns
			);
			$options = CMap::mergeArray($options, $this->options);
			$this->formRenderBegin();
			$this->widget('zii.widgets.grid.CGridView', $options);
			echo implode('', $buttons);
			$this->formRenderEnd();
		}
		else
		{
			$this->formRenderBegin();
			$options = array(
				'id' => $this->getDOMId().'-list',
				'dataProvider'=>$this->dataProvider(),
				'itemView' => $this->itemView()
			);
			$options = CMap::mergeArray($options, $this->options);
			$this->widget('zii.widgets.CListView', $options);
			$this->formRenderEnd();
		}
	}
	
	/**
	 * Renders the form beginning using {@link http://www.yiiframework.com/doc/api/1.1/CHtml#beginForm-detail CHtml::beginForm} or
	 * {@link UFormWorklet::taskRenderBegin}.
	 * @since 1.1.2
	 */
	public function taskFormRenderBegin()
	{
		$form = $this->form();
		if($form === false)
			return;
		if(is_array($form))
			echo CHtml::beginForm($form['action'],$form['method'],$form['htmlOptions']);
		elseif(is_string($form))
		{
			$w = wm()->get($form);
			$w->renderBegin();
		}
	}
	
	/**
	 * Renders the form beginning using {@link http://www.yiiframework.com/doc/api/1.1/CHtml#endForm-detail CHtml::endForm} or
	 * {@link UFormWorklet::taskRenderEnd}.
	 * @since 1.1.2
	 */
	public function taskFormRenderEnd()
	{
		$form = $this->form();
		if($form === false)
			return;
		if(is_array($form))
			echo CHtml::endForm();
		elseif(is_string($form))
		{
			$w = wm()->get($form);
			$w->renderEnd();
		}
	}
}