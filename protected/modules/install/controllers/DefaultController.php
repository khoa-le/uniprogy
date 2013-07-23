<?php
class DefaultController extends UController
{
	public $layout = 'install';
	
	public function init()
	{
		app()->theme = null;
		return parent::init();
	}
}