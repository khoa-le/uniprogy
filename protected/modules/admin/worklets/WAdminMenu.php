<?php
class WAdminMenu extends UMenuWorklet
{
	public $space = 'content';
	
	public function accessRules()
	{
		return array(array('allow','users'=>array('*')));
	}
	
	public function properties()
	{
		return array(
			'items'=>array(
				array('label'=>$this->t('Home'), 'url'=>array('/admin')),
				array('label'=>$this->t('Setup'), 'url'=>array('/admin/setup'), 'visible'=>app()->user->checkAccess('administrator')),
				array('label'=>$this->t('Tools'), 'url'=>array('/admin/tools'), 'visible'=>app()->user->checkAccess('administrator')),
				array('label'=>$this->t('Logout'), 'url'=>array('/admin/logout'), 'visible'=>app()->user->checkAccess('administrator')),
				array('label'=>$this->t('Site Index'), 'url'=>aUrl('/'), 'visible'=>app()->user->checkAccess('administrator')),
			),
			'htmlOptions'=>array(
				'class' => 'horizontal clearfix'
			)
		);
	}
}