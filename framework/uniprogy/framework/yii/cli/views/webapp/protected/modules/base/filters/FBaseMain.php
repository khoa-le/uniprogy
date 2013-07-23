<?php
class FBaseMain extends UWorkletFilter
{	
	public function filters()
	{
		return array(
			'base.init' => array('behaviors' => 'BaseOptimize'),			
		);
	}
	
	public function BaseOptimize()
	{
		if(!YII_DEBUG && file_exists($this->module->basePath.DS.'behaviors'.DS.'BBaseOptimize.php'))
			return array('base.optimize');
		return array();
	}
}