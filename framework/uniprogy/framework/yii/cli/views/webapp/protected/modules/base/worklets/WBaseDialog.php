<?php
class WBaseDialog extends UWidgetWorklet
{
	public $layout = null;
	public $options = array();
	public $space = 'outside';
	
	public function accessRules()
	{
		return array(array('allow','users'=>array('*')));
	}
	
	public function options()
	{
		return array(
			'autoOpen'=>false,
			'maxHeight'=>600,
			'width'=>600,
			'resizable'=>false,
			'modal'=>true,
		);
	}
	
	public function taskConfig()
	{
		$this->options = $this->options();
		parent::taskConfig();
	}
	
	public function taskRenderOutput()
	{
		app()->controller->beginWidget('zii.widgets.jui.CJuiDialog', array(
			'id'=>$this->getDOMId(),
			// additional javascript options for the dialog plugin
			'options'=>$this->options,
		));
		echo CHtml::tag('div', array('class' => 'content'), '');
		app()->controller->endWidget('zii.widgets.jui.CJuiDialog');
	}
}