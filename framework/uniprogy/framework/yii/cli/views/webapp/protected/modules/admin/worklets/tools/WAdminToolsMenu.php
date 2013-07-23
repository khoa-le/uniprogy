<?php
class WAdminToolsMenu extends UMenuWorklet
{
	public $space = 'sidebar';
	public $tools = array();
	
	public function accessRules()
	{
		return array(
			array('allow', 'roles' => array('administrator')),
			array('deny', 'users'=>array('*'))
		);
	}
	
	public function properties()
	{
		$r = array('items'=>array());
		foreach($this->tools as $id)
		{
			$w = wm()->get($id);
			$r['items'][] = array(
				'label' => $w->title(),
				'url' => url($w->getPath())
			);
		}
		
		return $r;
	}
}