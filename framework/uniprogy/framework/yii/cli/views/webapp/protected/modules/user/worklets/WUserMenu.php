<?php
class WUserMenu extends UMenuWorklet
{
	public $show = true;
	
	public function accessRules()
	{
		return array(array('allow','users'=>array('*')));
	}
	
	public function properties()
	{
		return array(
			'items'=>array(
				array('label'=>$this->t('Sign In'), 'url'=>array('/user/login'), 'visible' => app()->user->isGuest),
				array('label'=>$this->t('Sign Out'), 'url'=>array('/user/logout'), 'visible' => !app()->user->isGuest),
				array('label'=>$this->t('My Account'), 'url'=>array('/user/account'), 'visible' => !app()->user->isGuest)
			),
			'htmlOptions'=>array(
				'class' => 'horizontal clearfix'
			)
		);
	}
	
	public function taskRenderOutput()
	{
		if(!app()->user->isGuest)
		{
			$model = app()->user->model();
			$model->scenario = 'fullName';
			$this->render('welcome', array('model'=>$model));
		}
		parent::taskRenderOutput();
	}
}