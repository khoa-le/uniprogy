<?php
class WBasePage extends UWidgetWorklet
{	
	public function accessRules()
	{
		return array(array('allow','users'=>array('*')));
	}
	
	public function taskConfig()
	{
		if(app()->request->isAjaxRequest)
			$this->space = 'inside';
	}
	
	public function taskRenderOutput()
	{
		$action = Yii::createComponent(array('class' => 'UViewAction', 'layout' => false, 'basePath' => '/system/pages'), app()->controller, 'page');
		$action->run();
	}
}