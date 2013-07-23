<?php
class WUserAdminMenu extends UMenuWorklet
{
	public $space = 'sidebar';
	
	public function accessRules()
	{
		return array(
			array('allow','roles'=>array('administrator')),
			array('deny','users'=>array('*'))
		);
	}
	
	public function properties()
	{
		$p = array(
			'items'=>array(
				array('label'=>$this->t('Manage Users'), 'url'=>array('/user/admin/list')),
				array('label'=>$this->t('Create User'), 'url'=>array('/user/admin/create')),
			),
			'htmlOptions'=>array(
				//'class' => 'horizontal clearfix'
			)
		);
		return $p;
	}
}